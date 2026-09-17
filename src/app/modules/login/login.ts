import { notificar } from '../../services/avisos';
import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class LoginComponent {
  credenciales = {
    email: '',
    contrasenia: '',
  };

  errorAutenticacion: boolean = false;

  mensajeError: string = '';
  cargando: boolean = false;

  constructor(
    private router: Router,
    private authService: AuthService,
  ) {}

  ejecutarIngresar(): void {
    this.errorAutenticacion = false;
    this.mensajeError = '';

    this.cargando = true;

    this.authService
      .login(this.credenciales.email, this.credenciales.contrasenia)
      .subscribe({
        next: (respuesta) => {
          this.cargando = false;

          console.log('Respuesta del login:', respuesta);

          if (respuesta.success) {
            console.log('Login correcto');

            const usuario = respuesta.usuario;

            console.log('Usuario autenticado:', usuario);

            /*  LOGIN EXCLUSIVO PARA PACIENTES  */

            if (Number(usuario.id_rol) !== 3) {
              this.authService.logout();

              this.errorAutenticacion = true;
              this.mensajeError = notificar(
                'Esta cuenta pertenece a administracion. Utiliza el acceso administrativo',
                'error',
              );

              return;
            }

            //Paciente
            this.router.navigate(['/mis-citas']);
          } else {
            this.errorAutenticacion = true;
            this.mensajeError = notificar(
              respuesta.mensaje || 'Usuario o contraseña incorrecta',
              'error',
            );
          }
          /*   else if (usuario.id_rol === 2) {
            // MÉDICO
            this.router.navigate(['/']);
          } else if (usuario.id_rol === 3) {
            // ADMINISTRADOR
            this.router.navigate(['/mis-citas']);
          } else {
            this.router.navigate(['/']);
          } */
          /*  } else {
          this.errorAutenticacion = true;

          this.mensajeError = respuesta.mensaje || 'Usuario o contraseña incorrectos';
        }, */
        },

        error: (error) => {
          this.cargando = false;

          console.error('Error conectando con la API:', error);

          this.errorAutenticacion = true;

          this.mensajeError = notificar(
            error.error?.mensaje || 'No se pudo conectar con el servidor',
            'error',
          );
        },
      });
  }

  /* 
  constructor(private router: Router) {}

  ejecutarIngresar(): void {
    if (this.credenciales.correo.trim() === 'paciente@voltstack.com' && this.credenciales.contrasena === '123456') {
      this.errorAutenticacion = false;
      console.log('CRUD PHP [POST]: Autenticación de paciente en /api/login_paciente.php');
      // Redirige al historial de citas del cliente
      this.router.navigate(['/mis-citas']); 
    } else {
      this.errorAutenticacion = true;
    }
  }
 */
}
