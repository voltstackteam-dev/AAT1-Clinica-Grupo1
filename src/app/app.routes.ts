import { Routes } from '@angular/router';
import { PortalClinicaComponent } from './modules/portal-clinica/portal-clinica';
import { FarmaciaComponent } from './modules/farmacia/farmacia';
import { AgendaCitasComponent } from './modules/agenda-citas/agenda-citas';
import { HistorialCitasComponent } from './modules/historial-citas/historial-citas';
import { LoginComponent } from './modules/login/login';
import { RegistroComponent } from './modules/registro/registro';
import { AdminDashboardComponent } from './modules/admin-dashboard/admin-dashboard'; 

// IMPORTACIÓN DEL NUEVO LOGIN ADMINISTRATIVO SEGURO
import { AdminLoginComponent } from './modules/admin-login/admin-login';

export const routes: Routes = [
  { path: '', component: PortalClinicaComponent },
  { path: 'farmacia', component: FarmaciaComponent },
  { path: 'agenda-citas', component: AgendaCitasComponent },
  { path: 'agenda-citas/:id', component: AgendaCitasComponent },
  { path: 'mis-citas', component: HistorialCitasComponent },
  { path: 'login', component: LoginComponent },
  { path: 'registro', component: RegistroComponent },
  
  // 1. NUEVA PANTALLA INTERMEDIA DE AUTENTICACIÓN CORPORATIVA
  { path: 'login-personal', component: AdminLoginComponent },

  // 2. RUTA DE ACCESO PRIVADA DE ADMINISTRACIÓN
  { path: 'admin/control-citas', component: AdminDashboardComponent }, 
  
  { path: '**', redirectTo: '' }
];
