import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {

  private apiURL = 'http://localhost:8000/api/login.php';

  constructor(private http: HttpClient) {}

  usuario = signal<any>(this.leerUsuario());

  login(email: string, contrasenia: string): Observable<any> {

    const datos = {
      email,
      contrasenia: contrasenia
    };

    return this.http.post<any>(
      this.apiURL,
      datos
    ).pipe(

      tap((respuesta) => {

        if (respuesta.success) {

          localStorage.setItem(
            'token',
            respuesta.token
          );

          localStorage.setItem(
            'usuario',
            JSON.stringify(respuesta.usuario)
          );
          this.usuario.set(respuesta.usuario);

        }

      })

    );
  }


  logout(): void {

    localStorage.removeItem('token');

    localStorage.removeItem('usuario');
    this.usuario.set(null);

  }


  obtenerToken(): string | null {

    return localStorage.getItem('token');

  }


  obtenerUsuario(): any {
    return this.usuario();
  }


  estaAutenticado(): boolean {

    return this.obtenerToken() !== null;

  }

  private leerUsuario(): any {
    const usuario = localStorage.getItem('usuario');
    try { return usuario ? JSON.parse(usuario) : null; } catch { return null; }
  }

}
