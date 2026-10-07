import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardService } from '../services/dashboard';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './dashboard.html',
  styleUrls: ['./dashboard.css']
})
export class DashboardComponent implements OnInit {
  recentAppointments: any[] = [];
  cargando: boolean = true;

  metrics = [
    { title: 'Citas Hoy', value: '0', icon: '📅', color: '#3b82f6' },
    { title: 'Pacientes Nuevos', value: '0', icon: '👤', color: '#10b981' },
    { title: 'Ingresos del Mes', value: '$0', icon: '💰', color: '#f59e0b' },
    { title: 'Stock Bajo', value: '0', icon: '⚠️', color: '#ef4444' }
  ];

  lowStock = [
    { item: 'Amoxicilina 500mg', quantity: 3, min: 10 },
    { item: 'Ibuprofeno 400mg', quantity: 5, min: 15 },
    { item: 'Paracetamol 1g', quantity: 2, min: 20 }
  ];

  specialtyData = [
    { name: 'Cardiología', count: 15, percentage: 75 },
    { name: 'Pediatría', count: 10, percentage: 50 },
    { name: 'Traumatología', count: 8, percentage: 40 },
    { name: 'Neurología', count: 5, percentage: 25 }
  ];

  constructor(
    private dashboardService: DashboardService,
    private cdr: ChangeDetectorRef // 👈 Necesario por el zoneless
  ) {}

  ngOnInit(): void {
    this.dashboardService.getCitas().subscribe({
      next: (respuesta: any) => {
        console.log('Respuesta completa del backend:', respuesta);
        
        // Tu API devuelve { success: true, data: [...] }
        const citas = respuesta.data || respuesta;
        
        // Transformamos los datos al formato que usa la tabla
        this.recentAppointments = citas.map((c: any) => ({
          patient: c.paciente || 'Sin nombre',
          doctor: c.medico || 'Sin médico',
          time: this.formatearFecha(c.fecha_hora),
          status: this.traducirEstado(c.estado)
        }));

        // Actualizamos la métrica de citas
        this.metrics[0].value = citas.length.toString();
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (error) => {
        console.error('Error al conectar con PHP:', error);
        this.cargando = false;
        this.cdr.detectChanges();
      }
    });
  }

  formatearFecha(fecha: string): string {
    const d = new Date(fecha);
    return d.toLocaleString('es-GT', {
      day: '2-digit', month: '2-digit', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  }

  traducirEstado(estado: string): string {
    const mapa: any = {
      'PENDIENTE': 'En Espera',
      'CONFIRMADA': 'Confirmada',
      'EN_PROCESO': 'En Proceso',
      'COMPLETADA': 'Completada',
      'CANCELADA': 'Cancelada'
    };
    return mapa[estado] || estado;
  }
}