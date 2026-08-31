import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ClinicaService } from '../../../../services/clinica.service';

@Component({
  selector: 'app-seccion-especialidades',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './seccion-especialidades.html',
  styleUrl: './seccion-especialidades.css'
})
export class SeccionEspecialidades implements OnInit {
  private clinicaService = inject(ClinicaService);
  
  especialidades = signal<any[]>([]);

  // Descripciones originales por especialidad
  private descripciones: Record<string, string> = {
    'Cardiología': 'Prevención, diagnóstico y tratamiento de enfermedades del corazón y sistema circulatorio.',
    'Pediatría': 'Atención médica especializada, controles de crecimiento y desarrollo integral infantil.',
    'Traumatología': 'Cuidado de lesiones óseas y musculares con tratamientos quirúrgicos y de rehabilitación.',
    'Neurología': 'Diagnóstico avanzado y tratamiento de trastornos del cerebro y sistema nervioso central.'
  };

  ngOnInit(): void {
    this.cargarEspecialidades();
  }

  cargarEspecialidades(): void {
    this.clinicaService.getEspecialidades().subscribe({
      next: (res) => {
        if (res.success) {
          this.especialidades.set(res.data);
        }
      },
      error: (err) => console.error('Error al obtener especialidades:', err)
    });
  }

  getDescripcion(nombre: string): string {
    return this.descripciones[nombre] || `Atención médica especializada y cuidado integral en ${nombre}.`;
  }
}