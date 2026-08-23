import { Routes } from '@angular/router';
import { PortalClinicaComponent } from './modules/portal-clinica/portal-clinica';
import { FarmaciaComponent } from './modules/farmacia/farmacia';
import { AgendaCitasComponent } from './modules/agenda-citas/agenda-citas';
import { HistorialCitasComponent } from './modules/historial-citas/historial-citas';
import { LoginComponent } from './modules/login/login'; // <-- Importado
import { RegistroComponent } from './modules/registro/registro'; // <-- Importado

export const routes: Routes = [
  { path: '', component: PortalClinicaComponent },
  { path: 'farmacia', component: FarmaciaComponent },
  { path: 'agenda-citas', component: AgendaCitasComponent },
  { path: 'agenda-citas/:id', component: AgendaCitasComponent },
  { path: 'mis-citas', component: HistorialCitasComponent },
  { path: 'login', component: LoginComponent },       // <-- Ruta Login activa
  { path: 'registro', component: RegistroComponent },   // <-- Ruta Registro activa
  { path: '**', redirectTo: '' }
];
