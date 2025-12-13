"django motion models"
from django.db import models
from django.core.validators import MinValueValidator, MaxValueValidator


class Weight(models.Model):
    """Model for storing weight and body composition data"""
    GENDER_CHOICES = [
        (1, 'Male'),
        (2, 'Female'),
    ]

    person_id = models.IntegerField(default=1)
    date_time = models.DateTimeField()
    date = models.DateField()
    gender = models.IntegerField(choices=GENDER_CHOICES, default=1)
    age = models.IntegerField(validators=[MinValueValidator(0), MaxValueValidator(150)])
    height = models.DecimalField(max_digits=5, decimal_places=1, help_text="Height in cm")
    weight = models.DecimalField(max_digits=5, decimal_places=1, help_text="Weight in kg")
    fat = models.DecimalField(max_digits=5, decimal_places=1, null=True, blank=True, help_text="Body fat percentage")
    bone = models.DecimalField(max_digits=5, decimal_places=1, null=True, blank=True, help_text="Bone mass in kg")
    muscle = models.DecimalField(max_digits=5, decimal_places=1, null=True, blank=True, help_text="Muscle mass in kg")
    vfat = models.DecimalField(max_digits=5, decimal_places=1, null=True, blank=True, help_text="Visceral fat")
    moisture = models.DecimalField(max_digits=5, decimal_places=1, null=True, blank=True, help_text="Body moisture percentage")
    calorie = models.IntegerField(null=True, blank=True, help_text="Daily calorie intake")
    bmi = models.DecimalField(max_digits=5, decimal_places=1, null=True, blank=True, help_text="Body Mass Index")
    muscle_p = models.DecimalField(max_digits=5, decimal_places=1, null=True, blank=True, help_text="Muscle percentage")

    class Meta:
        ordering = ['-date']
        indexes = [
            models.Index(fields=['person_id', 'date']),
        ]

    def __str__(self):
        return f"Weight record for {self.date}: {self.weight} kg"

    def save(self, *args, **kwargs):
        # Calculate BMI if not provided
        if not self.bmi and self.height and self.weight:
            height_m = float(self.height) / 100
            self.bmi = float(self.weight) / (height_m ** 2)
        super().save(*args, **kwargs)


class BloodPressure(models.Model):
    """Model for storing blood pressure (Blodtryk) data"""
    person_id = models.IntegerField(default=1)
    date = models.DateField()
    systolic = models.IntegerField(help_text="Systolic pressure (upper number)")
    diastolic = models.IntegerField(help_text="Diastolic pressure (lower number)")

    class Meta:
        ordering = ['-date']
        indexes = [
            models.Index(fields=['person_id', 'date']),
        ]
        verbose_name = "Blood Pressure"
        verbose_name_plural = "Blood Pressures"

    def __str__(self):
        return f"Blood pressure for {self.date}: {self.systolic}/{self.diastolic}"
