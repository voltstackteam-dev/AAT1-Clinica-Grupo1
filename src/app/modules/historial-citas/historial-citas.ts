import { notificar, confirmar } from '../../services/avisos';
import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-historial-citas',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './historial-citas.html',
  styleUrl: './historial-citas.css',
})
export class HistorialCitasComponent implements OnInit {
  private http = inject(HttpClient);
  private auth = inject(AuthService);
  private router = inject(Router);
  private api = 'http://localhost:8000/api/citas.php';
  misCitas = signal<any[]>([]);
  cargando = signal(true);
  
/*AGREGUE ESTO PARA EL HISTORIAL DE CITAS EN EL PORTAL DEL PACIENTE*/

  historialMedico = signal<any[]>([]);
cargandoHistorial = signal(true);
historialExpandido = signal<number | null>(null);

  mensaje = signal('');
  editando = signal<number | null>(null);
  actualizando = signal<number | null>(null);
  fechaNueva = signal('');
  horaNueva = signal('');
  hoy = new Date().toISOString().slice(0, 10);
  horas = Array.from(
    { length: 9 },
    (_, indice) => `${String(indice + 8).padStart(2, '0')}:00`,
  );

  ngOnInit(): void {
    const usuario = this.auth.obtenerUsuario();
    if (!usuario || Number(usuario.id_rol) !== 3) {
      this.router.navigate(['/login']);
      return;
    }
    this.cargarCitas();
    this.cargarHistorial();
  }
  cargarCitas(): void {
    const usuario = this.auth.obtenerUsuario();
    this.http
      .get<any>(`${this.api}?id_usuario=${usuario.id_usuario}`)
      .subscribe({
        next: (r) => {
          this.misCitas.set(r.data || []);
          this.cargando.set(false);
        },
        error: () => {
          this.mensaje.set(
            notificar('No se pudieron cargar tus citas.', 'error'),
          );
          this.cargando.set(false);
        },
      });
  }



cargarHistorial(): void {
  this.http
    .get<any>('http://localhost:8000/api/historial_medico.php')
    .subscribe({
      next: (respuesta) => {
        this.historialMedico.set(respuesta.data || []);
        this.cargandoHistorial.set(false);
      },
      error: (error) => {
        console.error('Error al cargar historial:', error);

        this.historialMedico.set([]);
        this.cargandoHistorial.set(false);

        this.mensaje.set(
          notificar(
            error.error?.mensaje ||
              'No se pudo cargar el historial médico.',
            'error',
          ),
        );
      },
    });
}

mostrarDetalles(id: number): void {
  this.historialExpandido.update((actual) =>
    actual === id ? null : id
  );
}



/*ESTO ES PARA VER LA RECETA*/

recetaSeleccionada = signal<any | null>(null);

verReceta(consulta: any): void {
  if (!consulta.receta) {
    this.mensaje.set(
      notificar('Esta consulta no tiene una receta médica.', 'warning')
    );
    return;
  }

  this.recetaSeleccionada.set(consulta);
}

cerrarReceta(): void {
  this.recetaSeleccionada.set(null);
}

imprimirReceta(): void {
  window.print();
}




  puedeGestionar(cita: any): boolean {
    return cita.estado === 'PENDIENTE';
  }

  async cancelar(cita: any): Promise<void> {
    if (!(await confirmar(`¿Deseas cancelar la cita #${cita.id_cita}?`))) {
      return;
    }
    this.actualizarEstado(cita, 'CANCELADA');
  }
  prepararReprogramacion(cita: any): void {
    const [fecha, hora] = String(cita.fecha_hora).split(' ');
    this.editando.set(cita.id_cita);
    this.fechaNueva.set(fecha || '');
    this.horaNueva.set((hora || '').slice(0, 5));
    this.mensaje.set('');
  }
  reprogramar(cita: any): void {
    if (!this.fechaNueva() || !this.horaNueva()) {
      this.mensaje.set(
        notificar(
          'Selecciona una fecha y hora para reprogramar la cita.',
          'warning',
        ),
      );
      return;
    }
    this.actualizando.set(cita.id_cita);
    this.http
      .put<any>(this.api, {
        id_cita: cita.id_cita,
        fecha_hora: `${this.fechaNueva()}T${this.horaNueva()}`,
      })
      .subscribe({
        next: (respuesta) => {
          this.misCitas.update((citas) =>
            citas.map((item) =>
              item.id_cita === cita.id_cita
                ? {
                    ...item,
                    estado: respuesta.estado,
                    fecha_hora: respuesta.fecha_hora,
                  }
                : item,
            ),
          );
          this.editando.set(null);
          this.actualizando.set(null);
          this.mensaje.set(notificar(respuesta.mensaje, 'success'));
        },
        error: (error) => {
          this.actualizando.set(null);
          this.mensaje.set(
            notificar(
              error.error?.mensaje || 'No se pudo reprogramar la cita.',
              'error',
            ),
          );
        },
      });
  }
  private actualizarEstado(cita: any, estado: string): void {
    this.actualizando.set(cita.id_cita);
    this.http.put<any>(this.api, { id_cita: cita.id_cita, estado }).subscribe({
      next: (respuesta) => {
        this.misCitas.update((citas) =>
          citas.map((item) =>
            item.id_cita === cita.id_cita
              ? { ...item, estado: respuesta.estado }
              : item,
          ),
        );
        this.actualizando.set(null);
        this.mensaje.set(notificar('La cita fue cancelada.', 'success'));
      },
      error: (error) => {
        this.actualizando.set(null);
        this.mensaje.set(
          notificar(
            error.error?.mensaje || 'No se pudo actualizar la cita.',
            'error',
          ),
        );
      },
    });
  }
}
