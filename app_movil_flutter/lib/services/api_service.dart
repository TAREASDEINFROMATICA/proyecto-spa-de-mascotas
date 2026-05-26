import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiService {
  static const String baseUrl = 'http://127.0.0.1:8000/api/mobile';

  final storage = const FlutterSecureStorage();

  Future<Map<String, dynamic>> login(String correo, String contrasena) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({'correo': correo, 'contrasena': contrasena}),
    );

    final data = jsonDecode(response.body);

    if (response.statusCode == 200 && data['success'] == true) {
      await storage.write(key: 'token', value: data['access_token']);
    }

    return data;
  }

  Future<Map<String, dynamic>> me() async {
    final token = await storage.read(key: 'token');

    final response = await http.get(
      Uri.parse('$baseUrl/me'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );

    return jsonDecode(response.body);
  }

  Future<void> logout() async {
    await storage.delete(key: 'token');
  }
}
