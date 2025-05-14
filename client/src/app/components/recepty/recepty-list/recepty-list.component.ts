import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { Recept } from '../../../entity/Recept';
import { ReceptService } from '../../../services/recept.service';
import { NgForOf, NgIf } from '@angular/common';

@Component({
  selector: 'app-recepty-list',
  imports: [RouterLink, NgForOf, NgIf],
  templateUrl: './recepty-list.component.html',
  standalone: true,
})
export class ReceptyListComponent implements OnInit {
  groupedRecepty: Record<string, Record<string, Recept[]>> = {};

  constructor(private receptService: ReceptService) {}

  ngOnInit(): void {
    this.fetchGrupedRecepty();
  }

  private fetchGrupedRecepty(): void {
    this.receptService.getGroupedRecepty().subscribe({
      next: (data) => {
        this.groupedRecepty = data;
      },
      error: (err) => {
        console.log('Error fetching data: ', err);
      },
    });
  }

  getCategories(): string[] {
    return Object.keys(this.groupedRecepty);
  }

  getTypes(category: string): string[] {
    return Object.keys(this.groupedRecepty[category]);
  }
}
