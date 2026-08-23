import { Component } from '@angular/core';
import { CommonModule, NgClass } from '@angular/common';
import { Router, RouterLink, RouterLinkActive } from '@angular/router'; // <-- Importamos Router

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, NgClass, RouterLink, RouterLinkActive], 
  templateUrl: './navbar.html',
  styleUrl: './navbar.css'
})
export class NavbarComponent {
  menuAbierto: boolean = false;

  // Inyectamos el enrutador en el constructor
  constructor(private router: Router) {}

  alternarMenu(): void {
    this.menuAbierto = !this.menuAbierto;
  }

  cerrarMenu(): void {
    this.menuAbierto = false;
  }

  // FUNCIÓN NUEVA: Lee la ruta del select y ejecuta el salto de pantalla
  manejarNavegacionSesion(event: Event): void {
    const elementoSelect = event.target as HTMLSelectElement;
    const rutaDestino = elementoSelect.value;
    
    if (rutaDestino) {
      this.router.navigate([rutaDestino]);
      elementoSelect.value = ''; // Resetea el selector para permitir clics futuros
    }
  }
}
