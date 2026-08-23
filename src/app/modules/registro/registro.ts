import { Component } from '@angular/core';
import { CommonModule } from '@angular/common'; // <-- CORREGIDO: Cambiado @angular/core por @angular/common
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './registro.html',
  styleUrl: './registro.css'
})
export class RegistroComponent {
  // Todo tu código lógico inferior se mantiene exactamente igual...
  nuevoUsuario = {
    nombre: '',
    correo: '',
    dpi: '',
    contrasena: ''
  };

  exitoRegistro: boolean = false;

  constructor(private router: Router) {}

  ejecutarRegistro(): void {
    this.exitoRegistro = true;
    
    setTimeout(() => {
      this.exitoRegistro = false;
      this.router.navigate(['/login']);
    }, 3000);
  }
}
