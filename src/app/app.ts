import { Component, inject, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CarritoService } from './services/carrito';
import { FooterComponent } from './components/footer/footer'; // ◄ Corregido: apunta a tu archivo footer.ts

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, FormsModule, FooterComponent], 
  templateUrl: './app.html',
  styleUrls: ['./app.css']
})
export class App {
  private carritoService = inject(CarritoService);

  textoUsuario: string = '';
  mostrarChipsSintomas: boolean = true;
  mostrarBotonesCheckout: boolean = false;
  mostrarModalCarrito: boolean = false;
  mostrarChatEmergente: boolean = false;

  // Controles de navegación y autenticación
  seccionActual: string = 'inicio'; 
  menuHamburguesaAbierto: boolean = false; 
  usuarioLogueado: boolean = false;
  nombreUsuario: string = '';

  pasoActual: string = 'buscando'; 
  direccionEntrega: string = '';
  telefonoContacto: string = '';
  
  // Lectura de Signals del Carrito
  itemsCarrito = computed(() => this.carritoService.items() || []);
  totalCarrito = computed(() => this.carritoService.obtenerTotal() || 0);
  cantidadItems = computed(() => (this.carritoService.items() || []).reduce((suma, item) => suma + item.cantidad, 0));

  listaSintomas = [
    { id: 1, nombre_sintoma: 'Fiebre / Dolor' },
    { id: 2, nombre_sintoma: 'Gripe / Tos' },
    { id: 3, nombre_sintoma: 'Estómago' }
  ];

  mensajes: any[] = [
    { 
      emisor: 'bot', 
      tipo: 'texto', 
      contenido: '¡Hola! Soy la Dra. AI, tu asistente virtual de la farmacia. ¿Qué malestar o medicamento buscas hoy?' 
    }
  ];

  irASeccion(seccion: string) {
    this.seccionActual = seccion;
    this.menuHamburguesaAbierto = false; 
  }

  alternarMenuHamburguesa() {
    this.menuHamburguesaAbierto = !this.menuHamburguesaAbierto;
  }

  procesarLogin(event: Event) {
    event.preventDefault();
    this.usuarioLogueado = true;
    this.nombreUsuario = 'Carlos'; 
    this.seccionActual = 'inicio';
    this.menuHamburguesaAbierto = false;
  }

  cerrarSesion() {
    this.usuarioLogueado = false;
    this.nombreUsuario = '';
    this.seccionActual = 'inicio';
  }

  abrirAsistenteEmergente() { this.mostrarChatEmergente = true; }
  cerrarAsistenteEmergente() { this.mostrarChatEmergente = false; }
  alternarModalCarrito() { this.mostrarModalCarrito = !this.mostrarModalCarrito; }

  async seleccionarSintoma(sintoma: any) {
    this.mensajes.push({ emisor: 'user', tipo: 'texto', contenido: sintoma.nombre_sintoma });
    this.mostrarChipsSintomas = false;

    this.mensajes.push({ emisor: 'bot', tipo: 'texto', idTemporal: 'cargando', contenido: '🤖 Consultando inventario...' });
    await new Promise(resolve => setTimeout(resolve, 1200));
    this.mensajes = this.mensajes.filter(msg => msg.idTemporal !== 'cargando');

    this.mensajes.push({ emisor: 'bot', tipo: 'texto', contenido: 'Esto es lo que tengo disponible:' });
    this.mensajes.push({
      emisor: 'bot',
      tipo: 'productos',
      productos: [
        { id: 1, nombre: 'Paracetamol', gramaje: '500mg (Caja)', precio: 25.00, requiere_receta: false },
        { id: 2, nombre: 'Amoxicilina', gramaje: '500mg (Caja)', precio: 65.00, requiere_receta: true }
      ]
    });
  }

  agregarAlCarrito(medicina: any) {
    this.carritoService.agregarProducto(medicina);
    this.mensajes.push({
      emisor: 'bot',
      tipo: 'texto',
      contenido: `🛒 Añadí "${medicina.nombre}" a tu orden. Cuenta actual: Q${this.totalCarrito().toFixed(2)}.`
    });
    this.mostrarBotonesCheckout = true;
  }

  iniciarCheckout() {
    this.mostrarBotonesCheckout = false;
    this.pasoActual = 'pidiendo_direccion';
    this.mensajes.push({ emisor: 'bot', tipo: 'texto', contenido: '📋 Escribe tu dirección exacta de envío aquí:' });
  }

  enviarTextoLibre() {
    if (!this.textoUsuario.trim()) return;
    const entradaCliente = this.textoUsuario.trim();
    this.mensajes.push({ emisor: 'user', tipo: 'texto', contenido: entradaCliente });
    this.textoUsuario = '';

    setTimeout(() => {
      if (this.pasoActual === 'pidiendo_direccion') {
        this.direccionEntrega = entradaCliente;
        this.pasoActual = 'pidiendo_telefono';
        this.mensajes.push({ emisor: 'bot', tipo: 'texto', contenido: '📍 Guardada. Ahora ingresa tu número de teléfono:' });
      } else if (this.pasoActual === 'pidiendo_telefono') {
        this.telefonoContacto = entradaCliente;
        this.pasoActual = 'finalizado';
        this.mensajes.push({ emisor: 'bot', tipo: 'texto', contenido: '🎉 Resumen final de tu pedido:' });
        this.mensajes.push({
          emisor: 'bot',
          tipo: 'resumen_factura',
          resumen: { direccion: this.direccionEntrega, telefono: this.telefonoContacto, total: this.totalCarrito() }
        });
      }
    }, 800);
  }

  finalizarOrdenFinal() {
    alert(`🚀 Pedido enviado a: ${this.direccionEntrega}.`);
    this.carritoService.limpiarCarrito();
    this.mensajes = [{ emisor: 'bot', tipo: 'texto', contenido: '¡Gracias por tu compra! ¿Algún otro síntoma?' }];
    this.mostrarChipsSintomas = true;
    this.pasoActual = 'buscando';
    this.mostrarChatEmergente = false;
  }

  cerrarChat() { this.mostrarChatEmergente = false; }
  dispararSubidaReceta() { alert('📷 Cámara activada.'); }
}
