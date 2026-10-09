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
  providedIn: 'root',
})
export class CarritoService {
  public items = signal<ItemCarrito[]>([]);

  constructor() {
    this.cargarCarrito();
  }

  private obtenerClaveCarrito(): string {
    const usuarioGuardado = localStorage.getItem('usuario');

    if (!usuarioGuardado) {
      return 'carrito_invitado';
    }

    try {
      const usuario = JSON.parse(usuarioGuardado);

      const idUsuario = usuario.id_usuario;

      if (idUsuario) {
        return `carrito_usuario_${idUsuario}`;
      }
    } catch {
      console.error('No se pudo leer el usuario guardado.');
    }

    return 'carrito_invitado';
  }

  private cargarCarrito(): void {
    const clave = this.obtenerClaveCarrito();
    const carritoGuardado = localStorage.getItem(clave);

    if (!carritoGuardado) {
      this.items.set([]);
      return;
    }

    try {
      const carrito: ItemCarrito[] = JSON.parse(carritoGuardado);
      this.items.set(carrito);
    } catch {
      this.items.set([]);
    }
  }

  private guardarCarrito(): void {
    const clave = this.obtenerClaveCarrito();

    localStorage.setItem(
      clave,
      JSON.stringify(this.items()),
    );
  }

  agregarProducto(producto: any): void {
    const listadoActual = this.items();

    const productoExistente = listadoActual.find(
      (item) => item.id === producto.id,
    );

    if (productoExistente) {
      productoExistente.cantidad += 1;

      this.items.set([...listadoActual]);
    } else {
      const nuevoItem: ItemCarrito = {
        id: producto.id,
        nombre: producto.nombre,
        gramaje: producto.gramaje,
        precio: producto.precio,
        cantidad: 1,
        requiere_receta: producto.requiere_receta,
      };

      this.items.set([...listadoActual, nuevoItem]);
    }

    this.guardarCarrito();
  }

  aumentarCantidad(id: number): void {
    const listado = this.items();

    const producto = listado.find((item) => item.id === id);

    if (producto) {
      producto.cantidad += 1;
      this.items.set([...listado]);
      this.guardarCarrito();
    }
  }

  disminuirCantidad(id: number): void {
    const listado = this.items();

    const producto = listado.find((item) => item.id === id);

    if (producto) {
      if (producto.cantidad > 1) {
        producto.cantidad -= 1;
      } else {
        this.items.set(
          listado.filter((item) => item.id !== id),
        );

        this.guardarCarrito();
        return;
      }

      this.items.set([...listado]);
      this.guardarCarrito();
    }
  }

  eliminarProducto(id: number): void {
    const nuevoListado = this.items().filter(
      (item) => item.id !== id,
    );

    this.items.set(nuevoListado);
    this.guardarCarrito();
  }

  obtenerTotal(): number {
    return this.items().reduce(
      (suma, item) => suma + item.precio * item.cantidad,
      0,
    );
  }

  verificarSiRequiereReceta(): boolean {
    return this.items().some(
      (item) => item.requiere_receta === true,
    );
  }

  limpiarCarrito(): void {
    this.items.set([]);
    this.guardarCarrito();
  }

  recargarCarrito(): void {
  this.cargarCarrito();
}
  
}