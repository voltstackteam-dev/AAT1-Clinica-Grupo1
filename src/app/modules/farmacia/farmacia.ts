import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { CarritoService } from '../../services/carrito';
import { FormsModule } from '@angular/forms';

export interface Medicamento {
  id_medicamento: number;
  nombre: string;
  categoria: string;
  precio: number;
  stock: number;
  imagen_url?: string;
}

@Component({
  selector: 'app-farmacia',
  standalone: true,
 imports: [CommonModule, FormsModule],
  templateUrl: './farmacia.html',
  styleUrl: './farmacia.css'
})
export class FarmaciaComponent implements OnInit {
  private http = inject(HttpClient);
  private carritoService = inject(CarritoService);
  private apiUrl = 'http://localhost:8000/backend/get_medicamentos.php';

  medicamentos = signal<Medicamento[]>([]);
  categoriaActual = signal<string>('Todos');

  ngOnInit() {
    this.cargarMedicamentos('Todos');
  }

  cargarMedicamentos(categoria: string) {
    this.categoriaActual.set(categoria);
    const url = categoria === 'Todos' ? this.apiUrl : `${this.apiUrl}?categoria=${encodeURIComponent(categoria)}`;

    this.http.get<any>(url).subscribe({
      next: (res) => {
        if (res.status === 'success') {
          this.medicamentos.set(res.data);
        }
      },
      error: (err) => console.error('Error al cargar medicamentos:', err)
    });
  }
  
  agregarAlCarrito(medicamento: Medicamento) {
  this.carritoService.agregarProducto({
    id: medicamento.id_medicamento,
    nombre: medicamento.nombre,
    gramaje: medicamento.categoria,
    precio: Number(medicamento.precio),
    requiere_receta: false
  });

  console.log('Producto agregado al carrito:', medicamento.nombre);
}

// --- ASISTENTE VIRTUAL VOLTY ---
chatAbierto: boolean = false;
mensajeUsuario: string = '';

historialMensajes = [
  {
    emisor: 'asistente',
    texto: '¡Hola! Soy Volty, tu asistente de la Farmacia Voltstack. ¿En qué puedo ayudarte hoy con tus medicamentos?'
  }
];

alternarChat(): void {
  this.chatAbierto = !this.chatAbierto;
}

enviarMensajeChat(): void {
  if (!this.mensajeUsuario.trim()) return;

  this.historialMensajes.push({
    emisor: 'usuario',
    texto: this.mensajeUsuario
  });

  const consulta = this.mensajeUsuario.toLowerCase();
  this.mensajeUsuario = '';

  setTimeout(() => {
    let respuestaBot =
      'Entiendo tu consulta. Para brindarte información exacta sobre dosis o recetas reguladas, por favor facilítame el nombre del medicamento o comunícate con un asesor clínico.';

    if (
      consulta.includes('paracetamol') ||
      consulta.includes('ibuprofeno') ||
      consulta.includes('dolor')
    ) {
      respuestaBot =
        'Contamos con analgésicos disponibles como Paracetamol e Ibuprofeno en stock de entrega inmediata sin receta.';
    } else if (
      consulta.includes('receta') ||
      consulta.includes('seguro')
    ) {
      respuestaBot =
        'Para medicamentos controlados, puedes cargar tu receta médica al momento de finalizar tu orden de compra en línea.';
    } else if (
      consulta.includes('horario') ||
      consulta.includes('tiempo')
    ) {
      respuestaBot =
        'Nuestra farmacia central atiende las 24 horas del día. Los despachos a domicilio toman un estimado de 30 a 45 minutos.';
    }

    this.historialMensajes.push({
      emisor: 'asistente',
      texto: respuestaBot
    });
  }, 600);
}
}