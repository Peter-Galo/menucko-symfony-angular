import { Component } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { ReceptService } from '../../../services/recept.service';
import { NgForOf, NgIf, TitleCasePipe } from '@angular/common';
import { Recept } from '../../../entity/Recept';

@Component({
  selector: 'app-recepty-add',
  imports: [RouterLink, ReactiveFormsModule, NgIf, TitleCasePipe, NgForOf],
  templateUrl: './recepty-add.component.html',
  standalone: true,
})
export class ReceptyAddComponent {
  receptForm: FormGroup;
  minMealDays: number = 1;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private receptService: ReceptService,
  ) {
    this.receptForm = this.formBuilder.group({
      title: ['', [Validators.required, Validators.minLength(3)]],
      category: ['masko', [Validators.required]], // Default to 'masko'
      type: [''], // Default empty for 'masko'
      days: [this.minMealDays, [Validators.required]],
    });

    // Watch for changes to 'category' and update 'type' based on the selection
    this.receptForm.get('category')?.valueChanges.subscribe((category) => {
      const typeValue = category === 'veg' ? 'slane' : null;
      this.receptForm.get('type')?.setValue(typeValue);
    });
  }

  // Check if the selected category is "veg"
  isVegSelected(): boolean {
    return this.receptForm.get('category')?.value === 'veg';
  }

  onSubmit(): void {
    if (this.receptForm.valid) {
      const formValue = { ...this.receptForm.value };

      // Remove 'type' if category is 'masko'
      if (formValue.category === 'masko') {
        delete formValue.type;
      }

      this.receptService.createRecept(formValue as Recept).subscribe(() => {
        // Navigate back to the list page
        this.router.navigate(['/recepty']);
      });
    }
  }
}
