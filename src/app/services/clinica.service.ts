import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ClinicaService {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost/api_citas';

  // 1. Obtener especialidades
  getEspecialidades(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_especialidades.php`);
  }

  // 2. Obtener médicos (con filtro opcional)
  getMedicos(idEspecialidad: number = 0): Observable<any> {
    const url = idEspecialidad > 0 
      ? `${this.apiUrl}/get_medicos.php?id_especialidad=${idEspecialidad}`
      : `${this.apiUrl}/get_medicos.php`;
    return this.http.get<any>(url);
  }

  // 3. Crear cita médica (POST)
  crearCita(datosCita: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/crear_cita.php`, datosCita);
  }
}