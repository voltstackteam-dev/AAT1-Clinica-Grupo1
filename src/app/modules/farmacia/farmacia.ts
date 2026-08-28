import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { CarritoService } from '../../services/carrito';

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
  imports: [CommonModule],
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
}