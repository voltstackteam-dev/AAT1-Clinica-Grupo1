import { Routes } from '@angular/router';
import { PortalClinicaComponent } from './modules/portal-clinica/portal-clinica';
import { FarmaciaComponent } from './modules/farmacia/farmacia';
import { AgendaCitasComponent } from './modules/agenda-citas/agenda-citas';
import { HistorialCitasComponent } from './modules/historial-citas/historial-citas'; // <-- 1. IMPORTANTE: Importamos el nuevo componente

export const routes: Routes = [
  { path: '', component: PortalClinicaComponent },
  { path: 'farmacia', component: FarmaciaComponent },
  { path: 'agenda-citas', component: AgendaCitasComponent },
  { path: 'agenda-citas/:id', component: AgendaCitasComponent },
  { path: 'mis-citas', component: HistorialCitasComponent }, // <-- 2. IMPORTANTE: Creamos la ruta de acceso
  { path: '**', redirectTo: '' }
];
