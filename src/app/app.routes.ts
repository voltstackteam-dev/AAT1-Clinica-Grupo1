import { Routes } from '@angular/router';
import { PortalClinicaComponent } from './modules/portal-clinica/portal-clinica';
import { FarmaciaComponent } from './modules/farmacia/farmacia';
import { AgendaCitasComponent } from './modules/agenda-citas/agenda-citas';
import { HistorialCitasComponent } from './modules/historial-citas/historial-citas';
import { LoginComponent } from './modules/login/login';
import { RegistroComponent } from './modules/registro/registro';
import { AdminLoginComponent } from './modules/admin-login/admin-login';
import { AdminDashboardComponent } from './modules/admin-dashboard/admin-dashboard';

// Importamos los componentes hijos directamente para sus rutas físicas dedicadas
import { SeccionServicios } from './modules/portal-clinica/components/seccion-servicios/seccion-servicios';
import { SeccionEspecialidades } from './modules/portal-clinica/components/seccion-especialidades/seccion-especialidades';
import { ListadoMedicosComponent } from './modules/portal-clinica/components/listado-medicos/listado-medicos';
import { InfoHospitalesComponent } from './modules/portal-clinica/components/info-hospitales/info-hospitales';

export const routes: Routes = [
  // Inicio / Home completo tradicional
  { path: '', component: PortalClinicaComponent },
  
  // RUTAS FÍSICAS DEDICADAS (Funcionarán idéntico a la Farmacia)
  { path: 'servicios', component: SeccionServicios },
  { path: 'especialidades', component: SeccionEspecialidades },
  { path: 'medicos', component: ListadoMedicosComponent },
  { path: 'hospitales', component: InfoHospitalesComponent },
  
  // Resto de módulos del sistema
  { path: 'farmacia', component: FarmaciaComponent },
  { path: 'agenda-citas', component: AgendaCitasComponent },
  { path: 'agenda-citas/:id', component: AgendaCitasComponent },
  { path: 'mis-citas', component: HistorialCitasComponent },
  { path: 'login', component: LoginComponent },
  { path: 'registro', component: RegistroComponent },
  { path: 'login-personal', component: AdminLoginComponent },
  { path: 'admin/control-citas', component: AdminDashboardComponent },
  
  { path: '**', redirectTo: '' }
];
