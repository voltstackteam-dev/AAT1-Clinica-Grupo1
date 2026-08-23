import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-admin-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-login.html',
  styleUrl: './admin-login.css'
})
export class AdminLoginComponent {

  // Modelo plano listo para mapearse a JSON en tu Backend PHP
  credencialesAdmin = {
    usuarioEmpleado: '',
    claveAcceso: ''
  };

  errorAutenticacion: boolean = false;

  constructor(private router: Router) {}

  // Simulación de Auth API
  ejecutarLoginAdmin(): void {
    const user = this.credencialesAdmin.usuarioEmpleado.trim();
    const pass = this.credencialesAdmin.claveAcceso;

    // Simulación local. En producción aquí se enviaría vía HttpClient.post() a tu PHP
    if ((user === 'doctor@voltstack.com' || user === 'admin@voltstack.com') && pass === 'admin123') {
      this.errorAutenticacion = false;
      
      console.log('CRUD PHP [POST]: Autenticación exitosa en /api/login_personal.php');
      // Redirección inmediata al panel de control de citas autorizado
      this.router.navigate(['/admin/control-citas']);
    } else {
      this.errorAutenticacion = true;
    }
  }
}

export class AdminLogin {}
