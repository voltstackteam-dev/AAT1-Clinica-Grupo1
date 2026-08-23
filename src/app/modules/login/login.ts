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

  // Simulación de validación de credenciales médicas
  ejecutarIngresar(): void {
    if (this.credenciales.correo === 'paciente@voltstack.com' && this.credenciales.contrasena === '123456') {
      this.errorAutenticacion = false;
      // Redirección inmediata al panel de citas tras loguearse con éxito
      this.router.navigate(['/mis-citas']); 
    } else {
      this.errorAutenticacion = true;
    }
  }
}

