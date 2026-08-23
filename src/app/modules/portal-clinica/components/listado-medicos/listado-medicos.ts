import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-listado-medicos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './listado-medicos.html',
  styleUrl: './listado-medicos.css'
})
export class ListadoMedicosComponent {
  
  // Listado mapeado uno a uno con los nombres de tus archivos en la carpeta public
  listaDoctores = [
    { 
      id: 1, 
      nombre: 'Dr. Alejandro Méndez', 
      especialidad: 'Cardiología',
      disponible: true,
      foto: '/DrAlejandroMendez.png' 
    },
    { 
      id: 2, 
      nombre: 'Dra. Elena Rostova', 
      especialidad: 'Pediatría',
      disponible: false,
      foto: '/DraElenaRostova.png' 
    },
    { 
      id: 3, 
      nombre: 'Dr. Carlos Mendoza', 
      especialidad: 'Traumatología',
      disponible: true,
      foto: '/DrCarlosMendoza.png' 
    },
    { 
      id: 4, 
      nombre: 'Dra. Sofía Martínez', 
      especialidad: 'Neurología',
      disponible: false,
      foto: '/DraSofiaMartinez.png' 
    },
    { 
      id: 5, 
      nombre: 'Dr. Ricardo Peralta', 
      especialidad: 'Medicina General',
      disponible: true,
      foto: '/DrRicardoPeralta.png' 
    }
  ];

  @Output() medicoSeleccionado = new EventEmitter<number>();

  seleccionar(id: number): void {
    this.medicoSeleccionado.emit(id);
  }
}
