import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-historial-citas',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './historial-citas.html',
  styleUrl: './historial-citas.css'
})
export class HistorialCitasComponent implements OnInit {

  // Listado simulado de citas activas del paciente desde la Base de Datos
  misCitas = [
    {
      id: 'V-8842',
      medico: 'Dr. Alejandro Méndez',
      especialidad: 'Cardiología',
      fecha: '2026-09-02',
      hora: '09:30 AM',
      sede: 'Sede Central (Zona 10)',
      estado: 'Confirmada'
    },
    {
      id: 'V-9104',
      medico: 'Dra. Sofía Martínez',
      especialidad: 'Neurología',
      fecha: '2026-09-15',
      hora: '14:00 PM',
      sede: 'Sede Norte (Zona 11)',
      estado: 'Pendiente'
    }
  ];

  constructor() {}

  ngOnInit(): void {}

  // Función para simular la cancelación de una cita médica
  cancelarCita(idCita: string): void {
    const confirmar = confirm(`¿Estás seguro de que deseas cancelar la cita con código ${idCita}?`);
    if (confirmar) {
      this.misCitas = this.misCitas.filter(cita => cita.id !== idCita);
      alert('La cita ha sido cancelada de forma exitosa.');
    }
  }
}
