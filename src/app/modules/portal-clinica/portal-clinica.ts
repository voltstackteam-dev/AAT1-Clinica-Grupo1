import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

// 1. IMPORTANTE: Revisa que la ruta de importación de tu Hero sea idéntica a esta
import { HeroHospital } from './components/hero-hospital/hero-hospital';

// Las otras importaciones de los hijos se mantienen abajo igual
import { SeccionServicios } from './components/seccion-servicios/seccion-servicios';
import { SeccionEspecialidades } from './components/seccion-especialidades/seccion-especialidades';
import { ListadoMedicosComponent } from './components/listado-medicos/listado-medicos';
import { InfoHospitales } from './components/info-hospitales/info-hospitales';

@Component({
  selector: 'app-portal-clinica',
  standalone: true,
  imports: [
    CommonModule,
    HeroHospital, // <-- 2. REGISTRA AL HERO AQUÍ para que Angular lea la etiqueta HTML
    SeccionServicios,
    SeccionEspecialidades,
    ListadoMedicosComponent,
    InfoHospitales
  ],
  templateUrl: './portal-clinica.html',
  styleUrl: './portal-clinica.css'
})
export class PortalClinicaComponent {
  constructor(private router: Router) {}

  redirigirCita(idMedico: number): void {
    this.router.navigate(['/agenda-citas', idMedico]);
  }
}
