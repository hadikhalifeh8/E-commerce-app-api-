import 'package:flutter/material.dart';
import 'package:get/get.dart';

class CustomTextTitleAuth extends StatelessWidget {

  //عشان بحتاجو بكذا محل Dynamic  Title  بخلي ال 
  final String text;

  const CustomTextTitleAuth({Key? key, required this.text}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return  Text(text.tr,
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.headline2,
            );
  }
}