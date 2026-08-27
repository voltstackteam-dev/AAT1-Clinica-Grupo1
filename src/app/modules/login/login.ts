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
  styleUrl: './login.css'
})
export class LoginComponent {
  
  credenciales = {
    nombre_usuario: '',
    contrasenia: ''
  };

  errorAutenticacion: boolean = false;

  mensajeError: string = '';
  cargando: boolean = false;

  constructor(
    private router: Router,
    private authService: AuthService
  ){}

   ejecutarIngresar(): void {

    this.errorAutenticacion = false;
    this.mensajeError = '';

    this.cargando = true;


    this.authService.login(
      this.credenciales.nombre_usuario,
      this.credenciales.contrasenia
    ).subscribe({

      next: (respuesta) => {

        this.cargando = false;

        console.log(
          'Respuesta del login:',
          respuesta
        );


        if (respuesta.success) {

          console.log(
            'Login correcto'
          );


          const usuario = respuesta.usuario;

          console.log(
            'Usuario autenticado:',
            usuario
          );


          /*  REDIRECCIÓN SEGÚN ROL  */

          if (usuario.id_rol === 1) {

            // PACIENTE
            this.router.navigate(['/mis-citas']);

          }

          else if (usuario.id_rol === 2) {

            // MÉDICO
            this.router.navigate(['/medico']);

          }

          else if (usuario.id_rol === 3) {

            // ADMINISTRADOR
            this.router.navigate(['/admin']);

          }

          else {

            this.router.navigate(['/']);

          }

        }

        else {

          this.errorAutenticacion = true;

          this.mensajeError =
            respuesta.mensaje ||
            'Usuario o contraseña incorrectos';

        }

      },


      error: (error) => {

        this.cargando = false;

        console.error(
          'Error conectando con la API:',
          error
        );

        this.errorAutenticacion = true;

        this.mensajeError =
          'No se pudo conectar con el servidor';

      }

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
