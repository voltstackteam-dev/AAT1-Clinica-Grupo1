import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {

  private apiURL = 'http://localhost:8000/api/login.php';

  constructor(private http: HttpClient) {}

  login(nombre_usuario: string, contrasenia: string): Observable<any> {

    const datos = {
      nombre_usuario: nombre_usuario,
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

        }

      })

    );
  }


  logout(): void {

    localStorage.removeItem('token');

    localStorage.removeItem('usuario');

  }


  obtenerToken(): string | null {

    return localStorage.getItem('token');

  }


  obtenerUsuario(): any {

    const usuario = localStorage.getItem('usuario');

    if (usuario) {

      return JSON.parse(usuario);

    }

    return null;
  }


  estaAutenticado(): boolean {

    return this.obtenerToken() !== null;

  }

}