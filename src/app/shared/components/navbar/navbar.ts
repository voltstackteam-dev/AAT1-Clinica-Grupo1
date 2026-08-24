import { Component } from '@angular/core';
import { CommonModule, NgClass } from '@angular/common';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, NgClass, RouterLink, RouterLinkActive],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css'
})
export class NavbarComponent {
  menuAbierto: boolean = false;

  constructor(private router: Router) {}

  alternarMenu(): void {
    this.menuAbierto = !this.menuAbierto;
  }

  cerrarMenu(): void {
    this.menuAbierto = false;
  }

  manejarNavegacionSesion(event: Event): void {
    const elementoSelect = event.target as HTMLSelectElement;
    const rutaDestino = elementoSelect.value;
    
    if (rutaDestino) {
      this.router.navigate([rutaDestino]);
      elementoSelect.value = ''; 
    }
  }
}
