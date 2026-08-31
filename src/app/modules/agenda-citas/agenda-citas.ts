import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-agenda-citas',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './agenda-citas.html',
  styleUrl: './agenda-citas.css'
})
export class AgendaCitasComponent implements OnInit {

  private http = inject(HttpClient);
  private authService = inject(AuthService);

  private apiUrl = 'http://localhost:8000/api/citas.php';

  /* =========================
     DATOS DEL FORMULARIO
  ========================= */

  idMedico = signal<number>(0);
  idCliente = signal<number>(0);
  idSala = signal<number>(0);
  idHorario = signal<number>(0);

  motivoConsulta = signal('');

  /* =========================
     DATOS DE LA BASE DE DATOS
  ========================= */

  medicos = signal<any[]>([]);
  clientes = signal<any[]>([]);
  salas = signal<any[]>([]);
  horarios = signal<any[]>([]);

  /* =========================
     ESTADO
  ========================= */

  cargando = signal(false);
  cargandoDatos = signal(false);

  mensaje = signal('');
  error = signal(false);

  ngOnInit(): void {

    this.cargarDatos();

  }

  /* =========================
     CARGAR DATOS
  ========================= */

  cargarDatos(): void {

    this.cargandoDatos.set(true);

    const token = this.authService.obtenerToken();

    let headers = new HttpHeaders();

    if (token) {

      headers = headers.set(
        'Authorization',
        `Bearer ${token}`
      );

    }

    /*
     * Cargar médicos
     */

    this.http.get<any>(
      'http://localhost:8000/api/medicos.php',
      { headers }
    ).subscribe({

      next: (respuesta) => {

        console.log('Médicos:', respuesta);

        if (respuesta.success) {

          this.medicos.set(
            respuesta.data || []
          );

        }

      },

      error: (error) => {

        console.error(
          'Error cargando médicos:',
          error
        );

      }

    });


    /*
     * Cargar clientes
     */

    this.http.get<any>(
      'http://localhost:8000/api/clientes.php',
      { headers }
    ).subscribe({

      next: (respuesta) => {

        console.log('Clientes:', respuesta);

        if (respuesta.success) {

          this.clientes.set(
            respuesta.data || []
          );

        }

      },

      error: (error) => {

        console.error(
          'Error cargando clientes:',
          error
        );

      }

    });


    /*
     * Cargar salas
     */

    this.http.get<any>(
      'http://localhost:8000/api/salas.php',
      { headers }
    ).subscribe({

      next: (respuesta) => {

        console.log('Salas:', respuesta);

        if (respuesta.success) {

          this.salas.set(
            respuesta.data || []
          );

        }

      },

      error: (error) => {

        console.error(
          'Error cargando salas:',
          error
        );

      }

    });


    /*
     * Cargar horarios disponibles
     */

    this.http.get<any>(
      'http://localhost:8000/api/horarios.php',
      { headers }
    ).subscribe({

      next: (respuesta) => {

        console.log('Horarios:', respuesta);

        if (respuesta.success) {

          const disponibles = (respuesta.data || [])
            .filter((horario: any) =>
              Number(horario.disponibilidad) === 1
            );

          this.horarios.set(disponibles);

        }

        this.cargandoDatos.set(false);

      },

      error: (error) => {

        console.error(
          'Error cargando horarios:',
          error
        );

        this.cargandoDatos.set(false);

      }

    });

  }


  /* =========================
     CAMBIAR MÉDICO
  ========================= */

  cambiarMedico(): void {

    const medicoId = this.idMedico();

    console.log(
      'Médico seleccionado:',
      medicoId
    );

    /*
     * Buscar horarios disponibles
     * pertenecientes al médico seleccionado
     */

    if (medicoId === 0) {

      return;

    }

    const horariosFiltrados =
      this.horarios().filter(
        (horario: any) =>
          Number(horario.id_medico) === medicoId
      );

    console.log(
      'Horarios del médico:',
      horariosFiltrados
    );

    /*
     * Si el horario seleccionado
     * ya no pertenece al médico,
     * lo limpiamos.
     */

    if (
      !horariosFiltrados.some(
        (h: any) =>
          Number(h.id_horario) === this.idHorario()
      )
    ) {

      this.idHorario.set(0);

    }

  }


  /* =========================
     CREAR CITA
  ========================= */

  confirmarCita(): void {

    /*
     * Validaciones
     */

    if (this.idMedico() === 0) {

      this.error.set(true);

      this.mensaje.set(
        'Debes seleccionar un médico.'
      );

      return;

    }


    if (this.idCliente() === 0) {

      this.error.set(true);

      this.mensaje.set(
        'Debes seleccionar un cliente.'
      );

      return;

    }


    if (this.idSala() === 0) {

      this.error.set(true);

      this.mensaje.set(
        'Debes seleccionar una sala.'
      );

      return;

    }


    if (this.idHorario() === 0) {

      this.error.set(true);

      this.mensaje.set(
        'Debes seleccionar un horario.'
      );

      return;

    }


    if (!this.motivoConsulta().trim()) {

      this.error.set(true);

      this.mensaje.set(
        'Debes escribir el motivo de la consulta.'
      );

      return;

    }


    /*
     * Datos que espera citas.php
     */

    const datosCita = {

      id_medico: this.idMedico(),

      id_cliente: this.idCliente(),

      id_sala: this.idSala(),

      id_horario: this.idHorario(),

      motivo_consulta:
        this.motivoConsulta().trim()

    };


    console.log(
      'Datos enviados:',
      datosCita
    );


    this.cargando.set(true);

    this.error.set(false);

    this.mensaje.set('');


    /*
     * Token JWT
     */

    const token =
      this.authService.obtenerToken();


    let headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });


    if (token) {

      headers = headers.set(
        'Authorization',
        `Bearer ${token}`
      );

    }


    /*
     * POST
     */

    this.http.post<any>(
      this.apiUrl,
      datosCita,
      { headers }
    ).subscribe({

      next: (respuesta) => {

        console.log(
          'Respuesta API:',
          respuesta
        );

        this.cargando.set(false);


        if (respuesta.success) {

          this.error.set(false);

          this.mensaje.set(
            `Cita creada correctamente. ID de cita: ${respuesta.id_cita}`
          );


          /*
           * Limpiar motivo
           */

          this.motivoConsulta.set('');


          /*
           * El horario acaba de ser ocupado
           */

          this.idHorario.set(0);


          /*
           * Volver a cargar horarios
           */

          this.cargarHorarios();


        } else {

          this.error.set(true);

          this.mensaje.set(
            respuesta.mensaje ||
            'No se pudo crear la cita.'
          );

        }

      },


      error: (error) => {

        console.error(
          'Error creando cita:',
          error
        );

        this.cargando.set(false);

        this.error.set(true);

        this.mensaje.set(
          error.error?.mensaje ||
          'Error al conectar con la API.'
        );

      }

    });

  }


  /* =========================
     RECARGAR HORARIOS
  ========================= */

  cargarHorarios(): void {

    const token =
      this.authService.obtenerToken();


    let headers = new HttpHeaders();


    if (token) {

      headers = headers.set(
        'Authorization',
        `Bearer ${token}`
      );

    }


    this.http.get<any>(
      'http://localhost:8000/api/horarios.php',
      { headers }
    ).subscribe({

      next: (respuesta) => {

        console.log(
          'Horarios actualizados:',
          respuesta
        );


        if (respuesta.success) {

          const disponibles =
            (respuesta.data || [])
              .filter(
                (horario: any) =>
                  Number(horario.disponibilidad) === 1
              );


          this.horarios.set(
            disponibles
          );

        }

      },


      error: (error) => {

        console.error(
          'Error actualizando horarios:',
          error
        );

      }

    });

  }

}