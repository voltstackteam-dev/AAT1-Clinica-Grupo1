import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

import { HeroHospital } from './components/hero-hospital/hero-hospital';
import { SeccionServicios } from './components/seccion-servicios/seccion-servicios';
import { SeccionEspecialidades } from './components/seccion-especialidades/seccion-especialidades';
import { ListadoMedicosComponent } from './components/listado-medicos/listado-medicos';
import { InfoHospitalesComponent } from './components/info-hospitales/info-hospitales'; // <-- Corregido con Component

@Component({
  selector: 'app-portal-clinica',
  standalone: true,
  imports: [
    CommonModule,
    HeroHospital,
    SeccionServicios,
    SeccionEspecialidades,
    ListadoMedicosComponent,
    InfoHospitalesComponent // <-- Corregido con Component
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
