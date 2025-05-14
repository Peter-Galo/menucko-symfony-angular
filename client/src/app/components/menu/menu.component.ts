import { Component, OnInit } from '@angular/core';
import { MenuService } from '../../services/menu.service';
import { WeeklyMenu } from '../../entity/WeeklyMenu';
import { NgForOf } from '@angular/common';
import { ItemComponent } from './item/item.component';
import { saveAs } from 'file-saver';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  standalone: true,
  imports: [NgForOf, ItemComponent],
})
export class MenuComponent implements OnInit {
  menu: WeeklyMenu = { weekdays: [], weekend: [] };

  constructor(private menuService: MenuService) {}

  ngOnInit(): void {
    this.fetchMenu();
  }

  fetchMenu() {
    this.menuService.getWeeklyMenu().subscribe({
      next: (data: WeeklyMenu) => (this.menu = data),
      error: (err: any) => console.error('Failed to fetch menu:', err),
    });
  }

  generateWeeklyMenu(): void {
    this.menuService.generateWeeklyMenu().subscribe({
      next: (data: WeeklyMenu) => (this.menu = data),
      error: (err: any) => console.error('Failed to fetch menu:', err),
    });
  }

  downloadMenuPdf(): void {
    this.menuService.downloadWeeklyMenuPdf().subscribe({
      next: (pdfBlob) => {
        saveAs(pdfBlob, 'menu.pdf'); // Trigger the file download
      },
      error: (err) => console.error('Failed to download PDF:', err),
    });
  }
}
