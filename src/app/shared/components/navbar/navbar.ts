import { Component, inject, computed } from '@angular/core';
import { CommonModule, NgClass } from '@angular/common';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { CarritoService } from '../../../services/carrito';
import { CarritoComponent } from '../carrito/carrito';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, NgClass, RouterLink, RouterLinkActive, CarritoComponent],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css'
})
export class NavbarComponent {

  private carritoService = inject(CarritoService);

  cantidadItems = computed(() =>
    this.carritoService.items()
      .reduce((total, item) => total + item.cantidad, 0)
  );

  menuAbierto: boolean = false;
  carritoAbierto: boolean = false;

  constructor(private router: Router) {}

  alternarMenu(): void {
    this.menuAbierto = !this.menuAbierto;
  }

  cerrarMenu(): void {
    this.menuAbierto = false;
  }

  alternarCarrito(): void {
    this.carritoAbierto = !this.carritoAbierto;
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