import { Component, OnInit, inject, output, signal } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

interface Especialidad {
  id_especialidad: number;
  nombre_especialidad: string;
}

interface Medico {
  id_medico: number;
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
          'No se pudieron cargar los médicos. Vuelve a intentarlo.',
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
            'No se pudieron cargar las especialidades. Intenta recargarlas.',
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
    this.guardando.set(true);

    this.http
      .post<{ mensaje: string }>(`${this.api}/registrar_medico.php`, this.datos)
      .subscribe({
        next: (respuesta) => {
          this.guardando.set(false);
          this.datos = this.formularioVacio();
          formulario.resetForm(this.datos);
          this.exito.set(respuesta.mensaje);
          this.registrado.emit();
          this.cargarMedicos();
        },
        error: (error) => {
          this.guardando.set(false);
          this.error.set(
            error.error?.mensaje ||
              'No se pudo registrar el médico. Intenta nuevamente.',
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
