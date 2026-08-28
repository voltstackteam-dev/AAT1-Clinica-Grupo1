import { Component, inject, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CarritoService } from '../../../services/carrito';

@Component({
  selector: 'app-carrito',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './carrito.html',
  styleUrl: './carrito.css'
})
export class CarritoComponent {

  private carritoService = inject(CarritoService);

  items = computed(() => this.carritoService.items());

  total = computed(() => this.carritoService.obtenerTotal());

  cantidad = computed(() =>
    this.carritoService.items()
      .reduce((total, item) => total + item.cantidad, 0)
  );

  vaciarCarrito(): void {
    this.carritoService.limpiarCarrito();
  }
}