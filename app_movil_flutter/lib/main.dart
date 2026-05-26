import 'package:flutter/material.dart';
import 'services/api_service.dart';

void main() {
  runApp(const AppMovilSpa());
}

class AppMovilSpa extends StatelessWidget {
  const AppMovilSpa({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Spa de Mascotas',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(colorSchemeSeed: Colors.purple, useMaterial3: true),
      home: const LoginPage(),
    );
  }
}

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final correoController = TextEditingController();
  final contrasenaController = TextEditingController();

  final api = ApiService();

  bool cargando = false;
  String mensaje = '';

  Future<void> iniciarSesion() async {
    setState(() {
      cargando = true;
      mensaje = '';
    });

    try {
      final data = await api.login(
        correoController.text.trim(),
        contrasenaController.text.trim(),
      );

      if (data['success'] == true) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => InicioPage(user: data['user'])),
        );
      } else {
        setState(() {
          mensaje = data['message'] ?? 'No se pudo iniciar sesion';
        });
      }
    } catch (e) {
      setState(() {
        mensaje = 'Error de conexion con Laravel';
      });
    }

    setState(() {
      cargando = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xfff7f1ff),
      body: Center(
        child: Container(
          width: 380,
          padding: const EdgeInsets.all(25),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(22),
            boxShadow: const [
              BoxShadow(
                blurRadius: 12,
                color: Colors.black12,
                offset: Offset(0, 5),
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(Icons.pets, size: 70, color: Colors.purple),
              const SizedBox(height: 12),
              const Text(
                'Spa de Mascotas',
                style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 25),

              TextField(
                controller: correoController,
                decoration: const InputDecoration(
                  labelText: 'Correo',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.email),
                ),
              ),

              const SizedBox(height: 15),

              TextField(
                controller: contrasenaController,
                obscureText: true,
                decoration: const InputDecoration(
                  labelText: 'Contrasena',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.lock),
                ),
              ),

              const SizedBox(height: 20),

              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  onPressed: cargando ? null : iniciarSesion,
                  child:
                      cargando
                          ? const CircularProgressIndicator()
                          : const Text('Iniciar sesion'),
                ),
              ),

              if (mensaje.isNotEmpty) ...[
                const SizedBox(height: 15),
                Text(
                  mensaje,
                  style: const TextStyle(color: Colors.red),
                  textAlign: TextAlign.center,
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

class InicioPage extends StatelessWidget {
  final Map<String, dynamic> user;

  const InicioPage({super.key, required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Inicio'),
        backgroundColor: Colors.purple.shade100,
      ),
      body: Padding(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Icon(Icons.pets, size: 70, color: Colors.purple),
            const SizedBox(height: 20),
            Text(
              'Bienvenido, ${user['nombre_completo']}',
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            Text('Correo: ${user['correo']}'),
            Text('Rol: ${user['rol']}'),
            const SizedBox(height: 30),
            const Text(
              'Conexion con Laravel exitosa.',
              style: TextStyle(
                fontSize: 18,
                color: Colors.green,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
