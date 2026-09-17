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
        this.error.set('No se pudieron cargar las especialidades.');
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
      this.error.set('Introduce el nombre de la especialidad.');
      return;
    }

    this.guardando.set(true);
    const datos = {
      nombre_especialidad: nombre,
      descripcion: this.datos.descripcion.trim(),
    };

    this.http.post(this.url, datos).subscribe({
      next: () => {
        this.guardando.set(false);
        this.datos = { nombre_especialidad: '', descripcion: '' };
        formulario.resetForm(this.datos);
        this.exito.set(
          'Especialidad registrada. Ya puedes seleccionarla al registrar un médico.',
        );
        this.cargar();
      },
      error: (error) => {
        this.guardando.set(false);
        this.error.set(
          error.error?.mensaje || 'No se pudo registrar la especialidad.',
        );
      },
    });
  }
}
