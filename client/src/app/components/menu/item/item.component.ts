import { Component, Input } from '@angular/core';
import { NgIf } from '@angular/common';
import { DayMeal } from '../../../entity/DayMeal';

@Component({
  selector: 'app-item',
  imports: [NgIf],
  templateUrl: './item.component.html',
  standalone: true,
})
export class ItemComponent {
  @Input() item?: DayMeal;
}
