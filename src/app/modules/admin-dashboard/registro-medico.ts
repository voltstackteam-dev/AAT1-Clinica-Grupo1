import { notificar } from '../../services/avisos';

import { Component, OnInit, inject, output, signal } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

interface Especialidad {
  id_especialidad: number;
  nombre_especialidad: string;
}

interface Medico {
  id_medico: number;
  id_usuario: number;
  id_especialidad: number;
  nombre: string;
  apellido: string;
  nombre_especialidad: string;
  telefono: string;
  email: string;
  colegiado_num: string | null;
}

@Component({
  selector: 'app-registro-medico',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './registro-medico.html',
  styleUrl: './registro-medico.css',
})
export class RegistroMedicoComponent implements OnInit {
  private http = inject(HttpClient);
  private api = 'http://localhost:8000/api';

  registrado = output<void>();
  especialidades = signal<Especialidad[]>([]);
  medicos = signal<Medico[]>([]);
  cargandoMedicos = signal(false);
  errorMedicos = signal('');
  cargando = signal(false);
  guardando = signal(false);
  error = signal('');
  exito = signal('');
  datos = this.formularioVacio();
  editando = signal<Medico | null>(null);

  editar(medico: Medico): void {
    if (this.guardando()) {
      return;
    }

    this.editando.set(medico);
    this.datos = {
      nombre: medico.nombre,
      apellido: medico.apellido,
      telefono: medico.telefono,
      id_especialidad: String(medico.id_especialidad),
      colegiado_num: medico.colegiado_num || '',
      email: medico.email,
      contrasenia: '',
    };
    document
      .getElementById('formulario-medico')
      ?.scrollIntoView({ behavior: 'smooth' });
  }

  cancelarEdicion(formulario: NgForm): void {
    if (this.guardando()) {
      return;
    }

    this.editando.set(null);
    this.datos = this.formularioVacio();
    formulario.resetForm(this.datos);
  }

  ngOnInit(): void {
    this.cargarEspecialidades();
    this.cargarMedicos();
  }

  cargarMedicos(): void {
    if (this.cargandoMedicos()) {
      return;
    }

    this.cargandoMedicos.set(true);
    this.errorMedicos.set('');

    this.http.get<{ data: Medico[] }>(`${this.api}/medicos.php`).subscribe({
      next: (respuesta) => {
        this.medicos.set(respuesta.data || []);
        this.cargandoMedicos.set(false);
      },
      error: () => {
        this.errorMedicos.set(
          notificar(
            'No se pudieron cargar los médicos. Vuelve a intentarlo.',
            'error',
          ),
        );
        this.cargandoMedicos.set(false);
      },
    });
  }

  cargarEspecialidades(): void {
    this.cargando.set(true);
    this.error.set('');

    this.http
      .get<{ data: Especialidad[] }>(`${this.api}/especialidades.php`)
      .subscribe({
        next: (respuesta) => {
          this.especialidades.set(respuesta.data || []);
          this.cargando.set(false);
        },
        error: () => {
          this.error.set(
            notificar(
              'No se pudieron cargar las especialidades. Intenta recargarlas.',
              'error',
            ),
          );
          this.cargando.set(false);
        },
      });
  }

  registrar(formulario: NgForm): void {
    if (formulario.invalid || this.guardando() || this.cargando()) {
      formulario.control.markAllAsTouched();
      return;
    }

    this.error.set('');
    this.exito.set('');
    const medico = this.editando();
    const perfil = {
      nombre: this.datos.nombre.trim(),
      apellido: this.datos.apellido.trim(),
      telefono: this.datos.telefono.trim(),
      id_especialidad: Number(this.datos.id_especialidad),
      colegiado_num: this.datos.colegiado_num.trim() || null,
    };

    if (!perfil.nombre || !perfil.apellido || !perfil.telefono) {
      notificar('Completa nombre, apellido y teléfono.', 'warning');
      return;
    }

    this.guardando.set(true);
    const solicitud = medico
      ? this.http.put<{ mensaje: string }>(
          `${this.api}/medicos.php?id=${medico.id_medico}`,
          {
            ...perfil,
            id_usuario: medico.id_usuario,
          },
        )
      : this.http.post<{ mensaje: string }>(
          `${this.api}/registrar_medico.php`,
          this.datos,
        );

    solicitud.subscribe({
      next: (respuesta) => {
        this.guardando.set(false);
        this.editando.set(null);
        this.datos = this.formularioVacio();
        formulario.resetForm(this.datos);
        this.exito.set(notificar(respuesta.mensaje, 'success'));
        this.registrado.emit();
        this.cargarMedicos();
      },
      error: (error) => {
        this.guardando.set(false);
        this.error.set(
          notificar(
            error.error?.mensaje ||
              'No se pudieron guardar los datos del médico. Intenta nuevamente.',
            'error',
          ),
        );
      },
    });
  }

  private formularioVacio() {
    return {
      nombre: '',
      apellido: '',
      telefono: '',
      id_especialidad: '',
      colegiado_num: '',
      email: '',
      contrasenia: '',
    };
  }
}
