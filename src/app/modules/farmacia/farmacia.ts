import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-farmacia',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './farmacia.html',
  styleUrl: './farmacia.css'
})
export class FarmaciaComponent {
  
  // Categoría activa seleccionada para el filtrado
  categoriaSeleccionada: string = 'Todos';

  // Catálogo base de medicamentos actualizado con imágenes de la carpeta public
  productosFarmacia = [
    {
      id: 101,
      nombre: 'Paracetamol 500mg',
      categoria: 'Analgésicos',
      precio: 15.50,
      disponibilidad: true,
      foto: 'paracetamol.png' // <-- Actualizado a tu archivo local .png
    },
    {
      id: 102,
      nombre: 'Amoxicilina 875mg',
      categoria: 'Antibióticos',
      precio: 85.00,
      disponibilidad: true,
      foto: 'amoxicilina.png' // <-- Actualizado a tu archivo local .png
    },
    {
      id: 103,
      nombre: 'Ibuprofeno 400mg',
      categoria: 'Analgésicos',
      precio: 22.00,
      disponibilidad: true,
      foto: 'ibuprofeno.png' // <-- Actualizado a tu archivo local .png
    },
    {
      id: 104,
      nombre: 'Loratadina 10mg',
      categoria: 'Antihistamínicos',
      precio: 18.50,
      disponibilidad: false, // Producto agotado
      foto: 'loratadina.png' // <-- Actualizado a tu archivo local .png
    },
    {
      id: 105,
      nombre: 'Vitamina C 1g',
      categoria: 'Vitaminas',
      precio: 45.00,
      disponibilidad: true,
      foto: 'vitaminac.jpg' // <-- Actualizado a tu archivo local .jpg (Nota el cambio de extensión)
    }
  ];


  // --- PROPIEDADES DEL ASISTENTE VIRTUAL LÓGICO ---
  chatAbierto: boolean = false;
  mensajeUsuario: string = '';
  
  historialMensajes = [
    { emisor: 'asistente', texto: '¡Hola! Soy Volty, tu asistente de la Farmacia Voltstack. ¿En qué puedo ayudarte hoy con tus medicamentos?' }
  ];

  // Filtra los medicamentos en base a la categoría seleccionada
  get productosFiltrados() {
    if (this.categoriaSeleccionada === 'Todos') {
      return this.productosFarmacia;
    }
    return this.productosFarmacia.filter(p => p.categoria === this.categoriaSeleccionada);
  }

  // Simulación interactiva del botón de compras
  agregarAlCarrito(idProducto: number): void {
    const producto = this.productosFarmacia.find(p => p.id === idProducto);
    if (producto) {
      alert(`🛒 ${producto.nombre} ha sido añadido a tu orden de compra.`);
    }
  }

  // Alterna la visibilidad de la ventana del chat
  alternarChat(): void {
    this.chatAbierto = !this.chatAbierto;
  }

  // Envía el mensaje del usuario y simula la respuesta automática del bot
  enviarMensajeChat(): void {
    if (!this.mensajeUsuario.trim()) return;

    this.historialMensajes.push({
      emisor: 'usuario',
      texto: this.mensajeUsuario
    });

    const consulta = this.mensajeUsuario.toLowerCase();
    this.mensajeUsuario = ''; 

    setTimeout(() => {
      let respuestaBot = 'Entiendo tu consulta. Para brindarte información exacta sobre dosis o recetas reguladas, por favor facilítame el nombre del medicamento o comunícate con un asesor clínico.';

      if (consulta.includes('paracetamol') || consulta.includes('ibuprofeno') || consulta.includes('dolor')) {
        respuestaBot = 'Contamos con Analgésicos disponibles como Paracetamol e Ibuprofeno en stock de entrega inmediata sin receta.';
      } else if (consulta.includes('receta') || consulta.includes('seguro')) {
        respuestaBot = 'Para medicamentos controlados (como antibióticos), puedes cargar tu receta médica al momento de finalizar tu orden de compra en línea.';
      } else if (consulta.includes('horario') || consulta.includes('tiempo')) {
        respuestaBot = 'Nuestra farmacia central atiende las 24 horas del día. Los despachos a domicilio toman un estimado de 30 a 45 minutos.';
      }

      this.historialMensajes.push({
        emisor: 'asistente',
        texto: respuestaBot
      });
    }, 600);
  }
}
