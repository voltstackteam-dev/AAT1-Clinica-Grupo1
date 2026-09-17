import { notificar } from '../../services/avisos';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

interface Especialidad {
  id_especialidad: number;
  nombre_especialidad: string;
  descripcion: string | null;
}

@Component({
  selector: 'app-registro-especialidad',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './registro-especialidad.html',
  styleUrl: './registro-especialidad.css',
})
export class RegistroEspecialidadComponent implements OnInit {
  private http = inject(HttpClient);
  private url = 'http://localhost:8000/api/especialidades.php';

  especialidades = signal<Especialidad[]>([]);
  guardando = signal(false);
  cargando = signal(false);
  error = signal('');
  exito = signal('');
  datos = { nombre_especialidad: '', descripcion: '' };
  editando = signal<number | null>(null);

  editar(especialidad: Especialidad): void {
    if (this.guardando()) {
      return;
    }

    this.editando.set(especialidad.id_especialidad);
    this.datos = {
      nombre_especialidad: especialidad.nombre_especialidad,
      descripcion: especialidad.descripcion || '',
    };
    document
      .getElementById('formulario-especialidad')
      ?.scrollIntoView({ behavior: 'smooth' });
  }

  cancelarEdicion(formulario: NgForm): void {
    if (this.guardando()) {
      return;
    }

    this.editando.set(null);
    this.datos = { nombre_especialidad: '', descripcion: '' };
    formulario.resetForm(this.datos);
  }

  ngOnInit(): void {
    this.cargar();
  }

  cargar(): void {
    this.cargando.set(true);
    this.error.set('');

    this.http.get<{ data: Especialidad[] }>(this.url).subscribe({
      next: (respuesta) => {
        this.especialidades.set(respuesta.data || []);
        this.cargando.set(false);
      },
      error: () => {
        this.error.set(
          notificar('No se pudieron cargar las especialidades.', 'error'),
        );
        this.cargando.set(false);
      },
    });
  }

  registrar(formulario: NgForm): void {
    if (formulario.invalid || this.guardando()) {
      return;
    }

    const nombre = this.datos.nombre_especialidad.trim();
    this.error.set('');
    this.exito.set('');

    if (!nombre) {
      this.error.set(
        notificar('Introduce el nombre de la especialidad.', 'error'),
      );
      return;
    }

    this.guardando.set(true);
    const datos = {
      nombre_especialidad: nombre,
      descripcion: this.datos.descripcion.trim(),
    };

    const id = this.editando();
    const solicitud = id
      ? this.http.put(`${this.url}?id=${id}`, datos)
      : this.http.post(this.url, datos);

    solicitud.subscribe({
      next: () => {
        this.guardando.set(false);
        this.editando.set(null);
        this.datos = { nombre_especialidad: '', descripcion: '' };
        formulario.resetForm(this.datos);
        this.exito.set(
          notificar(
            id
              ? 'Especialidad actualizada correctamente.'
              : 'Especialidad registrada. Ya puedes seleccionarla al registrar un médico.',
            'success',
          ),
        );
        this.cargar();
      },
      error: (error) => {
        this.guardando.set(false);
        this.error.set(
          notificar(
            error.error?.mensaje || 'No se pudo guardar la especialidad.',
            'error',
          ),
        );
      },
    });
  }
}
