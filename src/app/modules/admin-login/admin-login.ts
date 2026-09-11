import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({ selector: 'app-admin-login', standalone: true, imports: [CommonModule, FormsModule], templateUrl: './admin-login.html', styleUrl: './admin-login.css' })
export class AdminLoginComponent {
  private router = inject(Router); private auth = inject(AuthService);
  credencialesAdmin = { email: '', contrasenia: '' };
  errorAutenticacion = false; mensajeError = ''; cargando = false;
  
  ejecutarLoginAdmin(): void { this.errorAutenticacion = false; this.cargando = true;
    this.auth.login(this.credencialesAdmin.email, this.credencialesAdmin.contrasenia).subscribe({ next: respuesta => { this.cargando = false; 
    if (!respuesta.success) return this.mostrarError(respuesta.mensaje || 'Credenciales incorrectas.'); const rol = respuesta.usuario.id_rol; 
    if (rol !== 1 && rol !== 2) { this.auth.logout(); return this.mostrarError('Esta cuenta no pertenece al personal médico o administrativo.'); 

    } 
    this.router.navigate(['/admin/control-citas']); }, error: error => { this.cargando = false; this.mostrarError(error.error?.mensaje || 'No se pudo iniciar sesión.'); } }); }
  private mostrarError(mensaje: string): void { this.errorAutenticacion = true; this.mensajeError = mensaje; }
}
