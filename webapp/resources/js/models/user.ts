export enum UserType {
	CUSTOMER = 'C',
	EMPLOYEE_COOK = 'EC',
	EMPLOYEE_DELIVERYMAN = 'ED',
	EMPLOYEE_MANAGER = 'EM'
}

export interface UserModel {
	id?: number;
	name?: string;
	email?: string;
	address?: string;
	password?: string;
	phone?: string;
	nif?: number | null;
	photo_url?: string;
	blocked?: number | boolean;

	type?: UserType;

	readonly created_at?: string;
	readonly updated_at?: string;
	readonly deleted_at?: string;
	readonly logged_at?: string;
	readonly available_at?: string;
	readonly email_verified_at?: string;
};
