import { Routes } from '@angular/router';
import { PortalClinicaComponent } from './modules/portal-clinica/portal-clinica';
import { AgendaCitasComponent } from './modules/agenda-citas/agenda-citas';
import { FarmaciaComponent } from './modules/farmacia/farmacia';

export const routes: Routes = [
  // Carga la pantalla principal unificada (Home)
  { path: '', component: PortalClinicaComponent },
  
  // Enlaces a los módulos independientes
  { path: 'agenda-citas', component: AgendaCitasComponent },
  { path: 'agenda-citas/:idMedico', component: AgendaCitasComponent },
  { path: 'farmacia', component: FarmaciaComponent },
  
  // Redirección por si se ingresa una URL incorrecta
  { path: '**', redirectTo: '' }
];
