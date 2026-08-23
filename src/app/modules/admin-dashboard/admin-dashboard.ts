import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-dashboard.html',
  styleUrl: './admin-dashboard.css'
})
export class AdminDashboardComponent implements OnInit {

  // Vista activa del panel de control
  filtroEstado: string = 'Todos';
  
  // Modelo de edición temporal para comentarios de la cita seleccionada
  citaEnEdicionId: string | null = null;
  comentarioTemporal: string = '';

  // Catálogo inicial que simula el resultado de un SELECT de PHP/MySQL
  citasAdministrativas = [
    {
      id_cita: 'V-8842',
      paciente: 'Juan Pérez',
      dpi: '2541 88942 0101',
      medico: 'Dr. Alejandro Méndez',
      especialidad: 'Cardiología',
      fecha: '2026-09-02',
      hora: '09:30 AM',
      estado: 'Pendiente',
      comentarios: ''
    },
    {
      id_cita: 'V-9104',
      paciente: 'María López',
      dpi: '1985 33214 0101',
      medico: 'Dra. Sofía Martínez',
      especialidad: 'Neurología',
      fecha: '2026-09-15',
      hora: '14:00 PM',
      estado: 'Confirmada',
      comentarios: 'Paciente requiere examen de reflejos previo.'
    }
  ];

  constructor() {}

  ngOnInit(): void {}

  // Filtrado reactivo en interfaz
  get citasFiltradas() {
    if (this.filtroEstado === 'Todos') {
      return this.citasAdministrativas;
    }
    return this.citasAdministrativas.filter(c => pXConvertir(c.estado) === this.filtroEstado);
  }

  // ==========================================================================
  // DISPARADORES LISTOS PARA CONEXIONES CRUD (PHP BACKEND ENDPOINTS)
  // ==========================================================================

  // 1. UPDATE: Modificar estado de la cita a 'Confirmada'
  aceptarCita(idCita: string): void {
    const cita = this.citasAdministrativas.find(c => c.id_cita === idCita);
    if (cita) {
      cita.estado = 'Confirmada';
      console.log(`CRUD PHP [PUT]: Enviar a /api/actualizar_estado.php -> id: ${idCita}, estado: Confirmada`);
    }
  }

  // 2. UPDATE: Abrir bloque de edición de bitácora médica
  iniciarEdicionComentario(idCita: string, comentarioActual: string): void {
    this.citaEnEdicionId = idCita;
    this.comentarioTemporal = comentarioActual;
  }

  // 3. UPDATE: Confirmar y guardar la bitácora de comentarios en caliente
  guardarComentario(idCita: string): void {
    const cita = this.citasAdministrativas.find(c => c.id_cita === idCita);
    if (cita) {
      cita.comentarios = this.comentarioTemporal;
      this.citaEnEdicionId = null; // Cierra la caja de texto
      console.log(`CRUD PHP [PUT]: Enviar a /api/guardar_comentario.php -> id: ${idCita}, comentarios: ${this.comentarioTemporal}`);
      alert('Comentarios médicos actualizados en el historial.');
    }
  }

  // 4. DELETE: Remover la cita del listado (Cancelación / Rechazo administrativo)
  eliminarCita(idCita: string): void {
    const confirmar = confirm(`¿Desea denegar y eliminar permanentemente la cita ${idCita} del sistema?`);
    if (confirmar) {
      this.citasAdministrativas = this.citasAdministrativas.filter(c => c.id_cita !== idCita);
      console.log(`CRUD PHP [DELETE]: Enviar a /api/eliminar_cita.php -> id: ${idCita}`);
    }
  }
}

// Función auxiliar interna para estandarizar cadenas
function pXConvertir(val: string): string {
  return val;
}

