import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AgendaCitas } from './agenda-citas';

describe('AgendaCitas', () => {
  let component: AgendaCitas;
  let fixture: ComponentFixture<AgendaCitas>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AgendaCitas],
    }).compileComponents();

    fixture = TestBed.createComponent(AgendaCitas);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
