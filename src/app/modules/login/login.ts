import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './login.html',
  styleUrl: './login.css'
})
export class LoginComponent {
  
  credenciales = {
    correo: '',
    contrasena: ''
  };

  errorAutenticacion: boolean = false;

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
}
