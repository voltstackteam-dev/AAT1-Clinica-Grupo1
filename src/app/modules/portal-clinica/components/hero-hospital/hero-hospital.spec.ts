import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HeroHospital } from './hero-hospital';

describe('HeroHospital', () => {
  let component: HeroHospital;
  let fixture: ComponentFixture<HeroHospital>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HeroHospital],
    }).compileComponents();

    fixture = TestBed.createComponent(HeroHospital);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
