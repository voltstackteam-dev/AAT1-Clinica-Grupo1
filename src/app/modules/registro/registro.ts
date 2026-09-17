import { notificar, mostrarAviso } from '../../services/avisos';
import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './registro.html',
  styleUrl: './registro.css',
})
export class RegistroComponent {
  private http = inject(HttpClient);
  private router = inject(Router);
  nuevoUsuario = {
    nombre: '',
    apellido: '',
    email: '',
    contrasenia: '',
    fecha_nacimiento: '',
    telefono: '',
    direccion: '',
  };
  exitoRegistro = false;
  mensajeError = '';
  ejecutarRegistro(): void {
    this.mensajeError = '';
    this.http
      .post<any>('http://localhost:8000/api/pacientes.php', this.nuevoUsuario)
      .subscribe({
        next: (r) => {
          if (!r.success) {
            this.mensajeError = notificar(
              r.mensaje || 'No se pudo crear la cuenta.',
              'error',
            );
            return;
          }
          this.exitoRegistro = true;
          void mostrarAviso(
            'Tu cuenta se registró correctamente. Ya puedes iniciar sesión.',
            'success',
          ).then(() => this.router.navigate(['/login']));
        },
        error: (e) =>
          (this.mensajeError = notificar(
            e.error?.mensaje || 'No se pudo crear la cuenta.',
            'error',
          )),
      });
  }
}
