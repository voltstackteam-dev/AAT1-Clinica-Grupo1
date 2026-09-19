import { notificar } from '../../services/avisos';
import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-agenda-citas',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './agenda-citas.html',
  styleUrl: './agenda-citas.css',
})
export class AgendaCitasComponent implements OnInit {
  private http = inject(HttpClient);
  private auth = inject(AuthService);
  private router = inject(Router);
  private api = 'http://localhost:8000/api';
  hoy = new Date().toISOString().slice(0, 10);
  idMedico = signal(0);
  idSala = signal(0);
  fecha = signal('');
  hora = signal('');
  motivoConsulta = signal('');
  medicos = signal<any[]>([]);
  salas = signal<any[]>([]);
  disponibilidades = signal<any[]>([]);
  horas = signal<string[]>([]);
  cargando = signal(false);
  mensaje = signal('');
  error = signal(false);
  sesionActiva = signal(false);
  esPaciente = signal(false);

  ngOnInit() {
    const usuario = this.auth.obtenerUsuario();
    if (!usuario) {
      this.mostrarError('Para agendar una cita, por favor inicia sesión primero.');
      return;
    }
    this.sesionActiva.set(true);
    if (Number(usuario.id_rol) !== 3) {
      this.mostrarError('La agenda pública está disponible únicamente para cuentas de paciente.');
      return;
    }
    this.esPaciente.set(true);
    this.cargar('medicos', this.medicos);
    this.cargar('salas', this.salas);
  }
  private cargar(recurso: string, destino: any) {
    this.http.get<any>(`${this.api}/${recurso}.php`).subscribe({
      next: (r) => destino.set(r.data || []),
      error: () => this.mostrarError(`No se pudo cargar ${recurso}.`),
    });
  }
  private consultaHoras = 0;
  cargandoHoras = signal(false);

  cambiarMedico() {
    this.actualizarHoras();
  }

  actualizarHoras() {
    const consulta = ++this.consultaHoras;
    this.hora.set('');
    this.horas.set([]);
    this.cargandoHoras.set(false);

    if (!this.idMedico() || !this.idSala() || !this.fecha()) {
      return;
    }

    this.cargandoHoras.set(true);
    this.http
      .get<{ success: boolean; data: string[] }>(this.api + '/horas_disponibles.php', {
        params: {
          id_medico: this.idMedico(),
          id_sala: this.idSala(),
          fecha: this.fecha(),
        },
      })
      .subscribe({
        next: (respuesta) => {
          if (consulta !== this.consultaHoras) return;
          this.horas.set(respuesta.data || []);
          this.cargandoHoras.set(false);
        },
        error: () => {
          if (consulta !== this.consultaHoras) return;
          this.cargandoHoras.set(false);
          this.mostrarError('No se pudieron consultar los horarios. Intenta de nuevo.');
        },
      });
  }

  confirmarCita() {
    if (
      this.cargandoHoras() ||
      !this.horas().includes(this.hora()) ||
      !this.esPaciente() ||
      !this.idMedico() ||
      !this.idSala() ||
      !this.fecha() ||
      !this.hora()
    ) {
      this.mostrarError('Completa todos los campos obligatorios.');
      return;
    }
    this.cargando.set(true);
    this.http
      .post<any>(`${this.api}/citas.php`, {
        id_medico: this.idMedico(),
        id_sala: this.idSala(),
        fecha_hora: `${this.fecha()}T${this.hora()}`,
        motivo_consulta: this.motivoConsulta().trim(),
      })
      .subscribe({
        next: (r) => {
          this.cargando.set(false);
          this.error.set(!r.success);
          this.mensaje.set(
            notificar(
              r.success ? `Cita creada correctamente` : r.mensaje,
              this.error() ? 'error' : 'success',
            ),
          );
          if (r.success) {
            this.actualizarHoras();
            this.motivoConsulta.set('');
          }
        },
        error: (e) => {
          this.cargando.set(false);
          if (e.status === 409) {
            this.actualizarHoras();
            return;
          }
          this.mostrarError(e.error?.mensaje || 'No se pudo crear la cita.');
        },
      });
  }
  irALogin() {
    this.router.navigate(['/login']);
  }
  private mostrarError(mensaje: string) {
    this.error.set(true);
    this.mensaje.set(notificar(mensaje, this.error() ? 'error' : 'success'));
  }
}
