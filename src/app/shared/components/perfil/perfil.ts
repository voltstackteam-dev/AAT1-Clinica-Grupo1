import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { PerfilService } from '../../../services/perfil.service';
import { AuthService } from '../../../services/auth.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-perfil',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './perfil.html',
  styleUrl: './perfil.css'
})
export class PerfilComponent implements OnInit {

  private perfilService = inject(PerfilService);
  private router = inject(Router);
  private authService = inject(AuthService);

  perfil: any = {
    email: '',
    contrasenia: ''
  };

  mensaje: string = '';
  error: string = '';

  ngOnInit(): void {
    this.cargarPerfil();
  }

  cargarPerfil(): void {
    this.perfilService.obtenerPerfil().subscribe({
      next: (respuesta: any) => {
        if (respuesta.success) {
          this.perfil.email = respuesta.data.email;
        }
      },
      error: (error: any) => {
        this.error = 'No se pudieron cargar los datos del perfil.';
        console.error(error);

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudieron cargar los datos de tu perfil.',
          confirmButtonText: 'Aceptar'
        });
      }
    });
  }

  actualizarPerfil(): void {
    this.mensaje = '';
    this.error = '';

    const datos = {
      email: this.perfil.email,
      contrasenia: this.perfil.contrasenia
    };

    this.perfilService.actualizarPerfil(datos).subscribe({
      next: (respuesta: any) => {
        if (respuesta.success) {

          Swal.fire({
            icon: 'success',
            title: '¡Datos actualizados!',
            text: 'Por seguridad, debes iniciar sesión nuevamente.',
            confirmButtonText: 'Iniciar sesión'
          }).then(() => {
            this.authService.logout();
            this.router.navigate(['/login']);
          });

        }
      },
      error: (error: any) => {
        this.error = 'No se pudo actualizar el perfil.';
        console.error(error);

        Swal.fire({
          icon: 'error',
          title: 'No se pudo actualizar',
          text: 'Ocurrió un problema al actualizar tus datos.',
          confirmButtonText: 'Aceptar'
        });
      }
    });
  }
}