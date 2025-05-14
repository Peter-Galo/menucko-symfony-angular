import { Routes } from '@angular/router';
import { MenuComponent } from './components/menu/menu.component';
import { ReceptyListComponent } from './components/recepty/recepty-list/recepty-list.component';
import { ReceptyAddComponent } from './components/recepty/recepty-add/recepty-add.component';

export const routes: Routes = [
  { path: '', redirectTo: '/menu', pathMatch: 'full' },
  { path: 'menu', component: MenuComponent, title: 'Menu' },
  {
    path: 'recepty',
    children: [
      {
        path: '',
        component: ReceptyListComponent,
        title: 'Recepty',
      },
      {
        path: 'add',
        component: ReceptyAddComponent,
        title: 'Nový recept',
      },
    ],
  },
  { path: '**', redirectTo: '/menu', pathMatch: 'full' },
];
