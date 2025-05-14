import { DayMeal } from './DayMeal';

export interface WeeklyMenu {
  weekdays: DayMeal[];
  weekend: DayMeal[];
}
