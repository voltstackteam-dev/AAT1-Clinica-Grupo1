import { Routes } from '@angular/router';
import { PortalClinicaComponent } from './modules/portal-clinica/portal-clinica';
import { FarmaciaComponent } from './modules/farmacia/farmacia';
import { AgendaCitasComponent } from './modules/agenda-citas/agenda-citas'; // <-- Añadido

export const routes: Routes = [
  { path: '', component: PortalClinicaComponent },
  { path: 'farmacia', component: FarmaciaComponent },
  { path: 'agenda-citas', component: AgendaCitasComponent },      // <-- Ruta sin parámetros
  { path: 'agenda-citas/:id', component: AgendaCitasComponent },  // <-- Ruta con ID de médico
  { path: '**', redirectTo: '' }
];
