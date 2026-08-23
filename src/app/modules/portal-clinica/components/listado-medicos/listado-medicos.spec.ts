import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ListadoMedicos } from './listado-medicos';

describe('ListadoMedicos', () => {
  let component: ListadoMedicos;
  let fixture: ComponentFixture<ListadoMedicos>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoMedicos],
    }).compileComponents();

    fixture = TestBed.createComponent(ListadoMedicos);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
