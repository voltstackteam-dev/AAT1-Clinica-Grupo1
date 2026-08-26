import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';

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
  private apiUrl = 'http://localhost/api_citas/get_medicamentos.php';

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
}