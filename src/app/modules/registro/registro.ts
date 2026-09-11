import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
@Component({ selector: 'app-registro', standalone: true, imports: [CommonModule, FormsModule, RouterLink], templateUrl: './registro.html', styleUrl: './registro.css' })
export class RegistroComponent {
  private http = inject(HttpClient); private router = inject(Router);
  nuevoUsuario = { nombre: '', apellido: '', email: '', contrasenia: '', fecha_nacimiento: '', telefono: '', direccion: '' }; exitoRegistro = false; mensajeError = '';
  ejecutarRegistro(): void { this.mensajeError = ''; this.http.post<any>('http://localhost:8000/api/pacientes.php', this.nuevoUsuario).subscribe({ next: r => { if (!r.success) { this.mensajeError = r.mensaje || 'No se pudo crear la cuenta.'; return; } this.exitoRegistro = true; setTimeout(() => this.router.navigate(['/login']), 1500); }, error: e => this.mensajeError = e.error?.mensaje || 'No se pudo crear la cuenta.' }); }
}
