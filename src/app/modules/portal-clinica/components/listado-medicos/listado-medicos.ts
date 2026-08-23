import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-listado-medicos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './listado-medicos.html', // <-- Asegúrate de que NO diga .component
  styleUrl: './listado-medicos.css'      // <-- Asegúrate de que NO diga .component
})

export class ListadoMedicosComponent {
  // Arreglo temporal simulando los médicos del mockup
  listaDoctores = [
    { id: 1, iniciales: 'DR' },
    { id: 2, iniciales: 'DR' },
    { id: 3, iniciales: 'DR' },
    { id: 4, iniciales: 'DR' },
    { id: 5, iniciales: 'DR' }
  ];

  // Evento que avisa al componente Padre cuando se selecciona un círculo
  @Output() medicoSeleccionado = new EventEmitter<number>();

  seleccionar(id: number): void {
    this.medicoSeleccionado.emit(id);
  }
}
