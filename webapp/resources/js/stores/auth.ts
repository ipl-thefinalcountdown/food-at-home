"use strict"

import axios, { AxiosResponse } from 'axios';
import { VuexModule, Module, Mutation, Action } from 'vuex-module-decorators';
import { UserAuthModel } from '../models/auth';
import { UserModel } from '../models/user';

@Module({ namespaced: true })
export class Auth extends VuexModule {
	public token: string = localStorage.getItem('user-token') || '';
	public user: UserModel = JSON.parse(atob(localStorage.getItem('user-data') || 'e30=' /* {} encoded */));
	public status: string = '';

  @Mutation
  public setAuthRequest(): void {
	this.status = 'loading'
  }

	@Mutation
  public setAuthSuccess(data: {token:string, user:UserModel}): void {
	this.status = 'success'
	this.token = data.token
	this.user = data.user
	}

	@Mutation
	public setAuthError(): void {
		this.status = 'error'
	}

	@Mutation
	public setAuthLogout(): void {
		this.status = '';
		this.token = '';
    }

    @Mutation
	public setAuthDelete(): void {
		this.status = '';
		this.token = '';
	}

  @Action
  public makeAuthRequest(user: UserAuthModel): Promise<AxiosResponse> {
		return new Promise((resolve, reject) => {
			this.context.commit('setAuthRequest')
			axios({
				url: 'login',
				data: user,
				method: 'POST'
			})
			.then(resp => {
				const token = resp.data.access_token
				const user = resp.data.user
				localStorage.setItem('user-token', token)
				localStorage.setItem('user-data', btoa(JSON.stringify(user)))
				axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
				this.context.commit('setAuthSuccess', {token, user})
				//this.context.dispatch(USER_REQUEST)
				resolve(resp)
			})
			.catch(err => {
				this.context.commit('setAuthError', err)
				localStorage.removeItem('user-token')
				localStorage.removeItem('user-data')
				reject(err)
			})
		})
	}

	@Action
	public makeAuthLogout(): Promise<void> {
		return new Promise((resolve, reject) => {
			this.context.commit('setAuthLogout')

			//query logout
			axios({ url: 'logout', method: 'POST' }).then(() => {
				// remove the axios default header
				delete axios.defaults.headers.common['Authorization']
			})

			localStorage.removeItem('user-token')
			localStorage.removeItem('user-data')
			resolve()
		})
    }

    @Action
	public makeAuthDelete(): Promise<void> {
		return new Promise((resolve, reject) => {
            this.context.commit('setAuthDelete')

            //query delete
			axios({ url: 'user', method: 'DELETE' }).then(() => {
				// remove the axios default header
				delete axios.defaults.headers.common['Authorization']
			})

			localStorage.removeItem('user-token')
			localStorage.removeItem('user-data')
			resolve()
		})
	}

	get isAuthenticated(): boolean {
		return !!this.token
	}
	get authStatus(): string {
		return this.status
	}

	get authUser(): UserModel {
		return this.user;
	}
}

export default Auth;
