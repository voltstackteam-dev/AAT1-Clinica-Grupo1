import { Injectable, signal } from '@angular/core';

export interface ItemCarrito {
  id: number;
  nombre: string;
  gramaje: string;
  precio: number;
  cantidad: number;
  requiere_receta: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class CarritoService {
  // Usamos un "signal" de Angular para que cualquier pantalla se entere si el carrito cambia
  public items = signal<ItemCarrito[]>([]);

  /**
   * Añade un medicamento al carrito o incrementa su cantidad si ya existe
   */
  agregarProducto(producto: any) {
    const listadoActual = this.items();
    const productoExistente = listadoActual.find(item => item.id === producto.id);

    if (productoExistente) {
      // Si ya está, le sumamos 1 a la cantidad
      productoExistente.cantidad += 1;
      this.items.set([...listadoActual]);
    } else {
      // Si es nuevo, lo agregamos con cantidad inicial = 1
      const nuevoItem: ItemCarrito = {
        id: producto.id,
        nombre: producto.nombre,
        gramaje: producto.gramaje,
        precio: producto.precio,
        cantidad: 1,
        requiere_receta: producto.requiere_receta
      };
      this.items.set([...listadoActual, nuevoItem]);
    }
  }

  /**
   * Calcula el costo total acumulado en el carrito
   */
  obtenerTotal(): number {
    return this.items().reduce((suma, item) => suma + (item.precio * item.cantidad), 0);
  }

  /**
   * Verifica si alguno de los medicamentos agregados al carrito pide receta médica obligatoria
   */
  verificarSiRequiereReceta(): boolean {
    return this.items().some(item => item.requiere_receta === true);
  }

  limpiarCarrito() {
    this.items.set([]);
  }
}
