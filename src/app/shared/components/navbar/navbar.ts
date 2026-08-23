import { Component } from '@angular/core';
import { CommonModule, NgClass } from '@angular/common'; // <-- Inyectamos NgClass y CommonModule
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-navbar',
  standalone: true,
  // REGISTRO OBLIGATORIO DE DIRECTIVAS AUTÓNOMAS
  imports: [CommonModule, NgClass, RouterLink, RouterLinkActive], 
  templateUrl: './navbar.html',
  styleUrl: './navbar.css'
})
export class NavbarComponent {
  menuAbierto: boolean = false;

  alternarMenu(): void {
    this.menuAbierto = !this.menuAbierto;
  }

  cerrarMenu(): void {
    this.menuAbierto = false;
  }
}
